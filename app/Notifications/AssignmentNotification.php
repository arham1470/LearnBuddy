<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public Assignment $assignment,
        public string $eventType, // 'published' or 'graded'
        public ?AssignmentSubmission $submission = null
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('assignment', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('assignment', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->assignment->course;

        if ($this->eventType === 'graded') {
            $pointsAwarded = $this->submission?->points_awarded ?? 0;
            $maxPoints = $this->assignment->max_points ?? 100;

            $mail = (new MailMessage)
                ->subject("Assignment Graded: {$this->assignment->title} · LearnBuddy")
                ->greeting("Hello, {$notifiable->name}!")
                ->line("Your submission for **{$this->assignment->title}** in **{$course->title}** has been reviewed by your lecturer.")
                ->line("Score: **{$pointsAwarded} / {$maxPoints} points**");

            if (!empty($this->submission?->feedback)) {
                $mail->line("Lecturer Feedback: \"{$this->submission->feedback}\"");
            }

            return $mail->action('View Feedback & Score', route('courses.assignments.show', ['course' => $course->slug, 'assignment' => $this->assignment->slug]));
        }

        $dueFormatted = $this->assignment->due_date
            ? $this->assignment->due_date->setTimezone('Asia/Colombo')->format('d M Y, h:i A')
            : 'No deadline';

        return (new MailMessage)
            ->subject("New Assignment: {$this->assignment->title} · LearnBuddy")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("A new assignment **{$this->assignment->title}** was published in **{$course->title}**.")
            ->line("Due Date: **{$dueFormatted} (Asia/Colombo)**")
            ->action('View Assignment Instructions', route('courses.assignments.show', ['course' => $course->slug, 'assignment' => $this->assignment->slug]));
    }

    public function toArray(object $notifiable): array
    {
        $course = $this->assignment->course;
        $isGraded = ($this->eventType === 'graded');

        $pointsAwarded = $this->submission?->points_awarded ?? 0;
        $maxPoints = $this->assignment->max_points ?? 100;

        return [
            'type' => 'assignment_'.$this->eventType,
            'title' => $isGraded ? 'Assignment Graded' : 'New Assignment Published',
            'body' => $isGraded
                ? "Your score for '{$this->assignment->title}' is ready: {$pointsAwarded} / {$maxPoints}."
                : "'{$this->assignment->title}' has been added to {$course->title}.",
            'icon' => $isGraded ? 'fas fa-clipboard-check' : 'fas fa-tasks',
            'action_url' => route('courses.assignments.show', ['course' => $course->slug, 'assignment' => $this->assignment->slug]),
            'metadata' => [
                'assignment_id' => $this->assignment->id,
                'course_id' => $course->id,
                'event_type' => $this->eventType,
            ],
        ];
    }
}
