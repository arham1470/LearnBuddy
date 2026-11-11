<?php

namespace App\Jobs;

use App\Models\CourseAnnouncement;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\CourseAnnouncementNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCourseAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public CourseAnnouncement $announcement
    ) {}

    public function handle(NotificationDispatcherService $dispatcher): void
    {
        $course = $this->announcement->course;

        // Fetch all active enrolled students for this course
        $enrolledStudents = User::whereHas('enrollments', function ($query) use ($course) {
            $query->where('course_id', $course->id)
                ->where('status', Enrollment::STATUS_ACTIVE);
        })->get();

        Log::info("Dispatching announcement [{$this->announcement->id}] to {$enrolledStudents->count()} enrolled students.");

        foreach ($enrolledStudents as $student) {
            $dedupKey = "announcement_{$this->announcement->id}_{$student->id}";

            $dispatcher->send(
                user: $student,
                notification: new CourseAnnouncementNotification($this->announcement),
                deduplicationKey: $dedupKey,
                eventType: 'course_announcement'
            );
        }
    }
}
