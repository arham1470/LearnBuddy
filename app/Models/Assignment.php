<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_section_id',
        'lesson_id',
        'created_by_user_id',
        'title',
        'slug',
        'description',
        'instructions',
        'max_points',
        'passing_points',
        'due_date',
        'allowed_file_types',
        'max_file_size_mb',
        'is_published',
        'allow_late_submissions',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'is_published' => 'boolean',
            'allow_late_submissions' => 'boolean',
            'max_points' => 'integer',
            'passing_points' => 'integer',
            'max_file_size_mb' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Assignment $assignment) {
            if (empty($assignment->slug)) {
                $base = Str::slug($assignment->title);
                $slug = $base;
                $count = 1;
                while (static::where('course_id', $assignment->course_id)->where('slug', $slug)->exists()) {
                    $slug = "{$base}-{$count}";
                    $count++;
                }
                $assignment->slug = $slug;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function getUserSubmission(User $user): ?AssignmentSubmission
    {
        return $this->submissions()->where('user_id', $user->id)->first();
    }

    public function isPastDue(): bool
    {
        return $this->due_date !== null && $this->due_date->isPast();
    }

    public function canAcceptSubmission(): bool
    {
        if (! $this->is_published) {
            return false;
        }

        if ($this->isPastDue() && ! $this->allow_late_submissions) {
            return false;
        }

        return true;
    }

    public function allowedFileTypesArray(): array
    {
        if (empty($this->allowed_file_types)) {
            return ['pdf', 'zip', 'doc', 'docx', 'txt', 'png', 'jpg', 'jpeg'];
        }

        return array_map('trim', explode(',', strtolower($this->allowed_file_types)));
    }
}
