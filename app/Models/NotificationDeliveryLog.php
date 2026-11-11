<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationDeliveryLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event_type',
        'deduplication_key',
        'channel',
        'status',
        'error_message',
        'payload',
        'sent_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a notification with this deduplication key was already dispatched or queued.
     */
    public static function hasBeenSent(string $deduplicationKey): bool
    {
        return self::where('deduplication_key', $deduplicationKey)
            ->whereIn('status', ['sent', 'queued'])
            ->exists();
    }

    /**
     * Atomically reserve or record delivery attempt.
     */
    public static function recordDelivery(
        string $deduplicationKey,
        string $eventType,
        ?int $userId,
        string $channel = 'database',
        string $status = 'sent',
        ?string $errorMessage = null,
        array $payload = []
    ): ?self {
        try {
            return self::create([
                'user_id' => $userId,
                'event_type' => $eventType,
                'deduplication_key' => $deduplicationKey,
                'channel' => $channel,
                'status' => $status,
                'error_message' => $errorMessage,
                'payload' => $payload,
                'sent_at' => ($status === 'sent') ? now() : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Already exists (duplicate caught by unique index constraint)
            return null;
        }
    }
}
