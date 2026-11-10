<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'file_name',
        'file_path',
        'file_size_bytes',
        'file_type',
        'external_url',
        'is_downloadable',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'is_downloadable' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function formattedFileSize(): string
    {
        if (! $this->file_size_bytes) {
            return '';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->file_size_bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 1).' '.$units[$pow];
    }
}
