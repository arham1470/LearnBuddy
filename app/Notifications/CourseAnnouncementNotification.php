<?php

namespace App\Notifications;

use App\Models\CourseAnnouncement;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public CourseAnnouncement $announcement
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('announcement', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('announcement', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->announcement->course;
        $authorName = $this->announcement->author?->name ?? 'Course Instructor';

        return (new MailMessage)
            ->subject("Course Announcement: {$this->announcement->title} · {$course->title}")
            ->greeting("Ayubowan, {$notifiable->name}!")
            ->line("An announcement was posted by **{$authorName}** in **{$course->title}**:")
            ->line("### {$this->announcement->title}")
            ->line($this->announcement->content)
            ->action('View Course Announcements', route('courses.announcements.index', $course))
            ->line('Keep up with your course updates on LearnBuddy.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_announcement',
            'title' => $this->announcement->title,
            'body' => $this->announcement->title.' in '.$this->announcement->course->title,
            'icon' => 'fas fa-bullhorn',
            'action_url' => route('courses.announcements.index', $this->announcement->course),
            'metadata' => [
                'announcement_id' => $this->announcement->id,
                'course_id' => $this->announcement->course_id,
            ],
        ];
    }
}
