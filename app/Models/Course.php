<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_REVIEW = 'review';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'thumbnail_url',
        'preview_video_url',
        'difficulty_level',
        'language',
        'duration_hours',
        'learning_outcomes',
        'prerequisites',
        'status',
        'published_at',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'decimal:2',
            'learning_outcomes' => 'array',
            'prerequisites' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_instructors')
            ->withPivot(['is_primary', 'can_manage_content', 'can_grade'])
            ->withTimestamps();
    }

    public function courseInstructors(): HasMany
    {
        return $this->hasMany(CourseInstructor::class);
    }

    public function deliveryOptions(): HasMany
    {
        return $this->hasMany(CourseDeliveryOption::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseSection::class, 'course_id', 'section_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function publishedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_published', true)->latest();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', Enrollment::STATUS_ACTIVE);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('created_by_user_id', $user->id)
                ->orWhereHas('instructors', function (Builder $instQ) use ($user) {
                    $instQ->where('user_id', $user->id);
                });
        });
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_REVIEW;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function hasInstructor(User $user): bool
    {
        if ($this->created_by_user_id === $user->id) {
            return true;
        }

        return $this->instructors->contains('id', $user->id);
    }

    public function isPrimaryInstructor(User $user): bool
    {
        if ($this->created_by_user_id === $user->id) {
            return true;
        }

        $instructor = $this->courseInstructors->firstWhere('user_id', $user->id);

        return $instructor ? (bool) $instructor->is_primary : false;
    }

    public function canInstructorManageContent(User $user): bool
    {
        if ($user->isAdmin() || $this->created_by_user_id === $user->id) {
            return true;
        }

        $instructor = $this->courseInstructors->firstWhere('user_id', $user->id);

        return $instructor ? (bool) $instructor->can_manage_content : false;
    }

    public function deliveryModes(): array
    {
        return $this->deliveryOptions->pluck('delivery_mode')->unique()->values()->all();
    }

    public function formattedDeliveryModes(): string
    {
        $modes = $this->deliveryModes();

        if (empty($modes)) {
            return 'Not specified';
        }

        $labels = array_map(function ($mode) {
            return match ($mode) {
                'online' => 'Online',
                'physical' => 'Physical Classroom',
                'hybrid' => 'Hybrid (Online + Physical)',
                default => ucfirst($mode),
            };
        }, $modes);

        return implode(' & ', $labels);
    }

    public function lowestPrice(): ?float
    {
        $activeOptions = $this->deliveryOptions->where('is_active', true);

        if ($activeOptions->isEmpty()) {
            return null;
        }

        return (float) $activeOptions->min('price');
    }

    public function primaryInstructor(): ?User
    {
        return $this->instructors->firstWhere('pivot.is_primary', true)
            ?? $this->instructors->first()
            ?? $this->creator;
    }

    public function formattedPriceRange(): string
    {
        $activeOptions = $this->deliveryOptions->where('is_active', true);

        if ($activeOptions->isEmpty()) {
            return 'Price on inquiry';
        }

        $allFree = $activeOptions->every(fn ($opt) => $opt->is_free || (float) $opt->price === 0.0);
        if ($allFree) {
            return 'Free';
        }

        $min = (float) $activeOptions->min('price');
        $max = (float) $activeOptions->max('price');
        $currency = $activeOptions->first()->currency ?? 'LKR';

        if ($min === $max) {
            return $currency.' '.number_format($min, 2);
        }

        if ($min === 0.0) {
            return 'Free / Up to '.$currency.' '.number_format($max, 2);
        }

        return 'From '.$currency.' '.number_format($min, 2);
    }

    public function formattedDuration(): ?string
    {
        if (empty($this->duration_hours) || (float) $this->duration_hours <= 0) {
            return null;
        }

        $hours = (float) $this->duration_hours;
        if (floor($hours) == $hours) {
            return ((int) $hours).'h';
        }

        $wholeHours = floor($hours);
        $minutes = round(($hours - $wholeHours) * 60);

        return $wholeHours > 0 ? "{$wholeHours}h {$minutes}m" : "{$minutes}m";
    }

    public function averageRating(): ?float
    {
        $avg = $this->publishedReviews->avg('rating');

        return $avg ? round((float) $avg, 1) : null;
    }

    public function reviewsCount(): int
    {
        return $this->publishedReviews->count();
    }

    public function isUserEnrolled(User $user): bool
    {
        return $this->enrollments()
            ->where('user_id', $user->id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_PENDING_PAYMENT])
            ->exists();
    }

    public function getUserEnrollment(User $user): ?Enrollment
    {
        return $this->enrollments()
            ->where('user_id', $user->id)
            ->latest()
            ->first();
    }
}
