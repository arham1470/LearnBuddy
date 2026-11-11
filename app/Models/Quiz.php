<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quiz extends Model
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
        'time_limit_minutes',
        'passing_percentage',
        'max_attempts',
        'is_published',
        'show_correct_answers_after_attempt',
    ];

    protected function casts(): array
    {
        return [
            'time_limit_minutes' => 'integer',
            'passing_percentage' => 'integer',
            'max_attempts' => 'integer',
            'is_published' => 'boolean',
            'show_correct_answers_after_attempt' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quiz $quiz) {
            if (empty($quiz->slug)) {
                $base = Str::slug($quiz->title);
                $slug = $base;
                $count = 1;
                while (static::where('course_id', $quiz->course_id)->where('slug', $slug)->exists()) {
                    $slug = "{$base}-{$count}";
                    $count++;
                }
                $quiz->slug = $slug;
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

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function totalMarks(): int
    {
        return (int) $this->questions()->sum('marks');
    }

    public function userAttemptsCount(User $user): int
    {
        return $this->attempts()->where('user_id', $user->id)->count();
    }

    public function hasUserExceededAttempts(User $user): bool
    {
        if ($this->max_attempts === 0) {
            return false; // 0 = unlimited
        }

        return $this->userAttemptsCount($user) >= $this->max_attempts;
    }

    public function canUserAttempt(User $user): bool
    {
        if (! $this->is_published) {
            return false;
        }

        return ! $this->hasUserExceededAttempts($user);
    }

    public function getUserBestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_COMPLETED)
            ->orderByDesc('percentage')
            ->first();
    }

    public function getUserLatestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();
    }
}
