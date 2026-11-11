<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_GRADED = 'graded';

    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_original_name',
        'file_size_bytes',
        'file_mime_type',
        'student_notes',
        'submitted_at',
        'is_late',
        'status',
        'points_awarded',
        'feedback',
        'graded_by_user_id',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'is_late' => 'boolean',
            'points_awarded' => 'integer',
            'file_size_bytes' => 'integer',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_user_id');
    }

    public function isGraded(): bool
    {
        return $this->status === self::STATUS_GRADED;
    }

    public function isPassing(): bool
    {
        if (! $this->isGraded() || $this->points_awarded === null) {
            return false;
        }

        if ($this->assignment && $this->assignment->passing_points !== null) {
            return $this->points_awarded >= $this->assignment->passing_points;
        }

        return true;
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size_bytes;
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 2).' MB';
    }
}
