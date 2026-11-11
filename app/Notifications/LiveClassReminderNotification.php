<?php

namespace App\Notifications;

use App\Models\LiveClass;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveClassReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public LiveClass $liveClass,
        public int $minutesBefore = 60
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('live_class', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('live_class', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $timeColombo = $this->liveClass->scheduled_start_time->setTimezone('Asia/Colombo')->format('h:i A (d M Y)');
        $joinUrl = route('courses.live-classes.join', [$this->liveClass->course, $this->liveClass]);

        return (new MailMessage)
            ->subject("Live Class Reminder: {$this->liveClass->title} · LearnBuddy")
            ->greeting("Ayubowan, {$notifiable->name}!")
            ->line("Reminder: Your interactive live class **{$this->liveClass->title}** for **{$this->liveClass->course->title}** starts in {$this->minutesBefore} minutes.")
            ->line("Scheduled Start Time: **{$timeColombo} (Asia/Colombo)**")
            ->action('Join Live Classroom', $joinUrl)
            ->line('Please ensure your microphone and camera are ready before entering.');
    }

    public function toArray(object $notifiable): array
    {
        $timeColombo = $this->liveClass->scheduled_start_time->setTimezone('Asia/Colombo')->format('h:i A');
        $joinUrl = route('courses.live-classes.join', [$this->liveClass->course, $this->liveClass]);

        return [
            'type' => 'live_class_reminder',
            'title' => 'Live Class Starting Soon',
            'body' => "{$this->liveClass->title} starts at {$timeColombo} (Asia/Colombo).",
            'icon' => 'fas fa-video',
            'action_url' => $joinUrl,
            'metadata' => [
                'live_class_id' => $this->liveClass->id,
                'course_id' => $this->liveClass->course_id,
                'start_time' => $this->liveClass->scheduled_start_time->toIso8601String(),
            ],
        ];
    }
}
