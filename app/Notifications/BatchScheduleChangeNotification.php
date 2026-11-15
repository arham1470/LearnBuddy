<?php

namespace App\Notifications;

use App\Models\CourseBatch;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchScheduleChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public CourseBatch $batch,
        public string $changeType, // 'schedule_update', 'cancelled'
        public ?string $reason = null
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
        $course = $this->batch->course();
        $isCancelled = ($this->changeType === 'cancelled');

        $mail = (new MailMessage)
            ->subject(($isCancelled ? 'Campus Batch Cancelled: ' : 'Campus Schedule Update: ').$this->batch->batch_code.' · LearnBuddy')
            ->greeting("Hello, {$notifiable->name}!");

        if ($isCancelled) {
            $mail->line("Physical intake batch **{$this->batch->batch_code}** has been cancelled.");
        } else {
            $mail->line("The classroom timetable for **{$this->batch->batch_code}** has been updated.");
        }

        if ($this->reason) {
            $mail->line("Note: {$this->reason}");
        }

        return $mail->action('View Batch Timetable', route('student.batches.show', $this->batch));
    }

    public function toArray(object $notifiable): array
    {
        $isCancelled = ($this->changeType === 'cancelled');

        return [
            'type' => 'batch_schedule_change',
            'title' => $isCancelled ? 'Campus Batch Cancelled' : 'Campus Timetable Updated',
            'body' => "Notice for batch {$this->batch->batch_code} ({$this->batch->location?->city_town}).",
            'icon' => 'fas fa-map-marker-alt',
            'action_url' => route('student.batches.show', $this->batch),
            'metadata' => [
                'batch_id' => $this->batch->id,
                'batch_code' => $this->batch->batch_code,
                'change_type' => $this->changeType,
            ],
        ];
    }
}
