<?php

namespace App\Notifications;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public Quiz $quiz,
        public QuizAttempt $attempt
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('quiz', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('quiz', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusWord = $this->attempt->is_passed ? 'PASSED' : 'COMPLETED';
        $totalMarks = $this->attempt->total_marks ?: $this->quiz->totalMarks();

        return (new MailMessage)
            ->subject("Quiz Result [{$statusWord}]: {$this->quiz->title} · LearnBuddy")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("Your attempt #{$this->attempt->attempt_number} for **{$this->quiz->title}** has been scored.")
            ->line("Score: **{$this->attempt->score} / {$totalMarks}** ({$this->attempt->percentage}%)")
            ->line('Status: **'.($this->attempt->is_passed ? 'Passed 🎉' : 'Needs Review').'**')
            ->action('View Quiz Summary', route('courses.quizzes.show', ['course' => $this->quiz->course->slug, 'quiz' => $this->quiz->slug]));
    }

    public function toArray(object $notifiable): array
    {
        $totalMarks = $this->attempt->total_marks ?: $this->quiz->totalMarks();

        return [
            'type' => 'quiz_result',
            'title' => 'Quiz Attempt Scored',
            'body' => "You scored {$this->attempt->score}/{$totalMarks} on '{$this->quiz->title}'.",
            'icon' => $this->attempt->is_passed ? 'fas fa-award' : 'fas fa-question-circle',
            'action_url' => route('courses.quizzes.show', ['course' => $this->quiz->course->slug, 'quiz' => $this->quiz->slug]),
            'metadata' => [
                'quiz_id' => $this->quiz->id,
                'attempt_id' => $this->attempt->id,
                'is_passed' => $this->attempt->is_passed,
                'score' => $this->attempt->score,
            ],
        ];
    }
}
