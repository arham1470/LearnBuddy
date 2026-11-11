<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\User;
use App\Notifications\LiveClassReminderNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLiveClassRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public int $windowMinutes = 60
    ) {}

    public function handle(NotificationDispatcherService $dispatcher): void
    {
        $now = Carbon::now('Asia/Colombo');
        $windowEnd = $now->copy()->addMinutes($this->windowMinutes);

        // Find upcoming scheduled live classes within the reminder window
        $upcomingClasses = LiveClass::where('status', LiveClass::STATUS_SCHEDULED)
            ->whereBetween('scheduled_start_time', [$now->toDateTimeString(), $windowEnd->toDateTimeString()])
            ->with(['course'])
            ->get();

        foreach ($upcomingClasses as $liveClass) {
            $enrolledStudents = User::whereHas('enrollments', function ($query) use ($liveClass) {
                $query->where('course_id', $liveClass->course_id)
                    ->where('status', Enrollment::STATUS_ACTIVE);
            })->get();

            foreach ($enrolledStudents as $student) {
                $dedupKey = "live_class_reminder_{$liveClass->id}_{$student->id}_{$this->windowMinutes}m";

                $dispatcher->send(
                    user: $student,
                    notification: new LiveClassReminderNotification($liveClass, $this->windowMinutes),
                    deduplicationKey: $dedupKey,
                    eventType: 'live_class_reminder'
                );
            }
        }
    }
}
