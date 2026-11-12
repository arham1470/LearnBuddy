<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_code',
        'verification_hash',
        'user_id',
        'course_id',
        'enrollment_id',
        'issue_date',
        'final_grade',
        'metadata',
        'is_revoked',
        'revoked_reason',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'final_grade' => 'decimal:2',
            'metadata' => 'array',
            'is_revoked' => 'boolean',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function isValid(): bool
    {
        return ! $this->is_revoked;
    }

    public function isRevoked(): bool
    {
        return $this->is_revoked;
    }

    public function revoke(string $reason): void
    {
        $this->update([
            'is_revoked' => true,
            'revoked_reason' => $reason,
            'revoked_at' => now(),
        ]);
    }

    public function verificationUrl(): string
    {
        return route('certificates.verify', $this->verification_hash);
    }

    public function formattedGrade(): ?string
    {
        return $this->final_grade !== null ? number_format((float) $this->final_grade, 1).'%' : null;
    }

    public static function generateCode(): string
    {
        return 'LB-CERT-'.date('Y').'-'.strtoupper(Str::random(8));
    }

    public static function generateVerificationHash(): string
    {
        return hash('sha256', Str::random(40).microtime(true));
    }
}
