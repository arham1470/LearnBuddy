<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'course_id',
        'course_delivery_option_id',
        'course_batch_id',
        'enrollment_code',
        'delivery_mode',
        'status',
        'price_paid',
        'currency',
        'is_free',
        'enrolled_at',
        'expires_at',
        'completed_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'price_paid' => 'decimal:2',
            'is_free' => 'boolean',
            'enrolled_at' => 'datetime',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function deliveryOption(): BelongsTo
    {
        return $this->belongsTo(CourseDeliveryOption::class, 'course_delivery_option_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'course_batch_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function attendanceRate(): float
    {
        if (! $this->batch) {
            return 0.0;
        }

        $totalSessions = $this->batch->sessions()->where('status', BatchSession::STATUS_COMPLETED)->count();
        if ($totalSessions === 0) {
            return 0.0;
        }

        $presentCount = $this->attendances()
            ->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])
            ->count();

        return round(($presentCount / $totalSessions) * 100, 1);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_PAYMENT);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPendingPayment(): bool
    {
        return $this->status === self::STATUS_PENDING_PAYMENT;
    }

    public function isPhysical(): bool
    {
        return $this->delivery_mode === 'physical' || ($this->batch !== null);
    }

    public function formattedPrice(): string
    {
        if ($this->is_free || (float) $this->price_paid === 0.0) {
            return 'Free';
        }

        return $this->currency.' '.number_format((float) $this->price_paid, 2);
    }
}
