<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchSession extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'batch_id',
        'session_date',
        'start_time',
        'end_time',
        'topic',
        'room_number',
        'status',
        'cancellation_reason',
        'conducted_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by_user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'batch_session_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function formattedTiming(): string
    {
        $start = Carbon::parse($this->start_time)->format('h:i A');
        $end = Carbon::parse($this->end_time)->format('h:i A');

        return "{$start} - {$end} (Asia/Colombo)";
    }
}
