<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CourseBatch extends Model
{
    use HasFactory;

    public const STATUS_UPCOMING = 'upcoming';

    public const STATUS_OPEN = 'open_for_enrollment';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'course_delivery_option_id',
        'location_id',
        'batch_code',
        'batch_name',
        'max_seats',
        'enrolled_seats',
        'registration_start_date',
        'registration_end_date',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'max_seats' => 'integer',
            'enrolled_seats' => 'integer',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function deliveryOption(): BelongsTo
    {
        return $this->belongsTo(CourseDeliveryOption::class, 'course_delivery_option_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(PhysicalLocation::class, 'location_id');
    }

    public function physicalLocation(): BelongsTo
    {
        return $this->location();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class, 'batch_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(BatchSession::class, 'batch_id')->orderBy('session_date')->orderBy('start_time');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_batch_id');
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_batch_id')->where('status', Enrollment::STATUS_ACTIVE);
    }

    public function scheduleChanges(): HasMany
    {
        return $this->hasMany(BatchScheduleChange::class, 'batch_id')->latest();
    }

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            CourseDeliveryOption::class,
            'id',
            'id',
            'course_delivery_option_id',
            'course_id'
        );
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_seats - $this->enrolled_seats);
    }

    public function isFull(): bool
    {
        return $this->enrolled_seats >= $this->max_seats;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function hasInstructor(User $user): bool
    {
        $course = $this->course ?? $this->deliveryOption?->course;

        return (bool) ($course?->hasInstructor($user));
    }

    public function logScheduleChange(User $user, string $changeType, array $prev = [], array $new = [], ?string $reason = null): BatchScheduleChange
    {
        return $this->scheduleChanges()->create([
            'changed_by_user_id' => $user->id,
            'change_type' => $changeType,
            'previous_details' => $prev,
            'new_details' => $new,
            'reason' => $reason,
        ]);
    }

    public function cancel(User $user, ?string $reason = null): void
    {
        $prev = ['status' => $this->status];
        $this->update(['status' => self::STATUS_CANCELLED]);
        $this->logScheduleChange($user, 'cancelled', $prev, ['status' => self::STATUS_CANCELLED], $reason);
    }
}
