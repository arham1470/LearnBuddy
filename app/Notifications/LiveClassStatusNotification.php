<?php

namespace App\Notifications;

use App\Models\LiveClass;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveClassStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public LiveClass $liveClass,
        public string $actionType, // 'cancelled' or 'rescheduled'
        public ?string $reason = null
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
        $isCancelled = ($this->actionType === 'cancelled');
        $statusWord = $isCancelled ? 'Cancelled' : 'Rescheduled';
        $timeColombo = $this->liveClass->scheduled_start_time->setTimezone('Asia/Colombo')->format('h:i A on d M Y');

        $mail = (new MailMessage)
            ->subject("Live Class {$statusWord}: {$this->liveClass->title} · LearnBuddy")
            ->greeting("Hello, {$notifiable->name}!");

        if ($isCancelled) {
            $mail->line("The live class session **{$this->liveClass->title}** for **{$this->liveClass->course->title}** has been cancelled.");
        } else {
            $mail->line("The live class session **{$this->liveClass->title}** has been rescheduled to **{$timeColombo} (Asia/Colombo)**.");
        }

        if ($this->reason) {
            $mail->line("Reason: {$this->reason}");
        }

        return $mail->action('View Course Schedule', route('learning.index', $this->liveClass->course));
    }

    public function toArray(object $notifiable): array
    {
        $isCancelled = ($this->actionType === 'cancelled');
        $title = $isCancelled ? 'Live Class Cancelled' : 'Live Class Rescheduled';
        $body = $isCancelled
            ? "Session '{$this->liveClass->title}' was cancelled."
            : "Session '{$this->liveClass->title}' has been moved to {$this->liveClass->scheduled_start_time->setTimezone('Asia/Colombo')->format('d M, h:i A')}.";

        return [
            'type' => 'live_class_status',
            'title' => $title,
            'body' => $body,
            'icon' => $isCancelled ? 'fas fa-calendar-times' : 'fas fa-calendar-alt',
            'action_url' => route('learning.index', $this->liveClass->course),
            'metadata' => [
                'live_class_id' => $this->liveClass->id,
                'action_type' => $this->actionType,
                'reason' => $this->reason,
            ],
        ];
    }
}
