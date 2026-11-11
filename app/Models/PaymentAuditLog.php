<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_id',
        'user_id',
        'event_type',
        'status_before',
        'status_after',
        'payload',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to log an audit trail entry.
     */
    public static function logEvent(
        string $eventType,
        ?Order $order = null,
        ?Payment $payment = null,
        ?string $statusBefore = null,
        ?string $statusAfter = null,
        array $payload = [],
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): self {
        return self::create([
            'order_id' => $order?->id ?? $payment?->order_id,
            'payment_id' => $payment?->id,
            'user_id' => $userId ?? $order?->user_id ?? $payment?->user_id,
            'event_type' => $eventType,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'payload' => $payload,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => now(),
        ]);
    }
}
