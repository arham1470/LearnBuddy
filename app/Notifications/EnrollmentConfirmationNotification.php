<?php

namespace App\Notifications;

use App\Models\Enrollment;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public Enrollment $enrollment
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
        $course = $this->enrollment->course;
        $deliveryMode = ucfirst($this->enrollment->delivery_mode);

        return (new MailMessage)
            ->subject("Enrolment Confirmed: {$course->title} · LearnBuddy")
            ->greeting("Ayubowan, {$notifiable->name}!")
            ->line("Your enrolment in **{$course->title}** ({$deliveryMode}) is confirmed and active.")
            ->line("Enrolment Code: **{$this->enrollment->enrollment_code}**")
            ->action('Access Course', route('learning.index', $course))
            ->line('Thank you for learning with LearnBuddy Sri Lanka!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'enrollment_confirmation',
            'title' => 'Enrolment Confirmed',
            'body' => "You have been enrolled in {$this->enrollment->course->title}.",
            'icon' => 'fas fa-graduation-cap',
            'action_url' => route('learning.index', $this->enrollment->course),
            'metadata' => [
                'course_id' => $this->enrollment->course_id,
                'enrollment_code' => $this->enrollment->enrollment_code,
                'delivery_mode' => $this->enrollment->delivery_mode,
            ],
        ];
    }
}
