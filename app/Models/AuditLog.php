<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log a tamper-resistant administrative or security action.
     */
    public static function log(
        string $action,
        string $description,
        ?Model $auditable = null,
        array $metadata = []
    ): self {
        // Redact any sensitive keys if accidentally passed in metadata
        $sanitizedMetadata = self::sanitizeMetadata($metadata);

        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'metadata' => $sanitizedMetadata,
            'created_at' => now(),
        ]);
    }

    protected static function sanitizeMetadata(array $data): array
    {
        $sensitiveKeys = ['password', 'secret', 'token', 'card', 'cvv', 'api_key', 'key', 'private_key'];

        foreach ($data as $k => $v) {
            foreach ($sensitiveKeys as $secret) {
                if (stripos((string) $k, $secret) !== false) {
                    $data[$k] = '***REDACTED***';
                    break;
                }
            }
            if (is_array($v)) {
                $data[$k] = self::sanitizeMetadata($v);
            }
        }

        return $data;
    }
}
