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
}
