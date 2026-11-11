<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    public const STATUS_PRESENT = 'present';

    public const STATUS_ABSENT = 'absent';

    public const STATUS_LATE = 'late';

    public const STATUS_EXCUSED = 'excused';

    protected $fillable = [
        'batch_session_id',
        'enrollment_id',
        'user_id',
        'status',
        'remarks',
        'recorded_by_user_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(BatchSession::class, 'batch_session_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE], true);
    }
}
