<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'type',
        'content',
        'video_provider',
        'video_asset_id',
        'video_playback_id',
        'video_upload_id',
        'video_status',
        'video_error_message',
        'video_playback_policy',
        'video_aspect_ratio',
        'duration_minutes',
        'sort_order',
        'is_preview',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'sort_order' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isCompletedByUser(User $user): bool
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->exists();
    }

    public function getUserProgress(User $user): ?LessonProgress
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->first();
    }
}
