<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public Course $course,
        public string $certificateCode
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('enrollment', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('enrollment', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Certificate of Completion: {$this->course->title} · LearnBuddy")
            ->greeting("Ayubowan, {$notifiable->name}!")
            ->line("Congratulations on completing **{$this->course->title}**!")
            ->line('Your official Certificate of Completion has been issued.')
            ->line("Certificate Credential ID: **{$this->certificateCode}**")
            ->action('View Course', route('learning.index', $this->course))
            ->line('Congratulations on your academic achievement with LearnBuddy!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certificate_issued',
            'title' => 'Certificate Issued',
            'body' => "Your certificate for '{$this->course->title}' is ready.",
            'icon' => 'fas fa-certificate',
            'action_url' => route('learning.index', $this->course),
            'metadata' => [
                'course_id' => $this->course->id,
                'certificate_code' => $this->certificateCode,
            ],
        ];
    }
}
