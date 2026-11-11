<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClass extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_LIVE = 'live';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const ACCESS_ENROLLED_STUDENTS = 'enrolled_students';

    public const ACCESS_BATCH_STUDENTS_ONLY = 'batch_students_only';

    public const ACCESS_INSTRUCTORS_ONLY = 'instructors_only';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'batch_id',
        'title',
        'description',
        'scheduled_start_time',
        'scheduled_end_time',
        'actual_start_time',
        'actual_end_time',
        'provider',
        'meeting_identifier',
        'meeting_password',
        'status',
        'access_permission',
        'cancellation_reason',
        'reminder_sent_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_start_time' => 'datetime',
            'scheduled_end_time' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'batch_id');
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isLive(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Cancel the live session with reason.
     */
    public function cancel(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Reschedule the live session.
     */
    public function reschedule(Carbon $newStart, Carbon $newEnd): void
    {
        $this->update([
            'scheduled_start_time' => $newStart,
            'scheduled_end_time' => $newEnd,
            'status' => self::STATUS_SCHEDULED,
        ]);
    }

    /**
     * Mark meeting as started live.
     */
    public function startLive(): void
    {
        $this->update([
            'status' => self::STATUS_LIVE,
            'actual_start_time' => now(),
        ]);
    }

    /**
     * Mark meeting as completed.
     */
    public function endLive(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'actual_end_time' => now(),
        ]);
    }

    /**
     * Check if a specific user can join this session right now.
     */
    public function canBeJoinedBy(User $user): bool
    {
        if ($this->isCancelled() || $this->isCompleted()) {
            return false;
        }

        $now = now();
        $isInstructorOrAdmin = $user->isAdmin() || ($this->instructor_id === $user->id) || $this->course->hasInstructor($user);

        // Instructors and admins can enter during the instructor buffer window (e.g. 30 min before) or while live
        if ($isInstructorOrAdmin) {
            $bufferMinutes = config('live_meeting.instructor_join_buffer_minutes', 30);
            $joinWindowStart = $this->scheduled_start_time->copy()->subMinutes($bufferMinutes);

            return $now->gte($joinWindowStart);
        }

        // For students: must have an active enrollment
        $enrollment = $this->course->activeEnrollments()->where('user_id', $user->id)->first();
        if (! $enrollment) {
            return false;
        }

        // If restricted to a specific batch
        if ($this->access_permission === self::ACCESS_BATCH_STUDENTS_ONLY && $this->batch_id) {
            if ((int) $enrollment->course_batch_id !== (int) $this->batch_id) {
                return false;
            }
        } elseif ($this->access_permission === self::ACCESS_INSTRUCTORS_ONLY) {
            return false;
        }

        // Check student time window: allowed from 15 minutes before scheduled start time until scheduled end time
        if ($this->isLive()) {
            return true;
        }

        $studentBuffer = config('live_meeting.student_join_buffer_minutes', 15);
        $studentJoinStart = $this->scheduled_start_time->copy()->subMinutes($studentBuffer);
        $studentJoinEnd = $this->scheduled_end_time->copy()->addMinutes(30); // allow slight overrun

        return $now->gte($studentJoinStart) && $now->lte($studentJoinEnd);
    }

    /**
     * Get time window status relative to user.
     */
    public function timeWindowStatus(User $user): string
    {
        if ($this->isCancelled()) {
            return 'cancelled';
        }

        if ($this->isCompleted()) {
            return 'ended';
        }

        if ($this->isLive()) {
            return 'joinable';
        }

        $now = now();
        $isInstructorOrAdmin = $user->isAdmin() || ($this->instructor_id === $user->id) || $this->course->hasInstructor($user);
        $bufferMinutes = $isInstructorOrAdmin
            ? config('live_meeting.instructor_join_buffer_minutes', 30)
            : config('live_meeting.student_join_buffer_minutes', 15);

        $joinStart = $this->scheduled_start_time->copy()->subMinutes($bufferMinutes);

        if ($now->lt($joinStart)) {
            return 'not_started';
        }

        if ($now->gt($this->scheduled_end_time->copy()->addMinutes(30))) {
            return 'ended';
        }

        return 'joinable';
    }

    /**
     * Formatted session timing string in Asia/Colombo time.
     */
    public function formattedTiming(): string
    {
        $start = $this->scheduled_start_time->timezone('Asia/Colombo');
        $end = $this->scheduled_end_time->timezone('Asia/Colombo');

        return $start->format('h:i A').' - '.$end->format('h:i A').' (Asia/Colombo)';
    }

    /**
     * Formatted date in Asia/Colombo time.
     */
    public function formattedDate(): string
    {
        return $this->scheduled_start_time->timezone('Asia/Colombo')->format('M d, Y (l)');
    }
}
