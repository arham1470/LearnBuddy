<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your Password · LearnBuddy Sri Lanka')
            ->greeting("Ayubowan, {$notifiable->name}!")
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required and your account remains secure.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'password_reset_requested',
            'title' => 'Password Reset Requested',
            'body' => 'A password reset link was sent to your email address.',
            'icon' => 'fas fa-key',
            'action_url' => route('password.request'),
            'metadata' => [],
        ];
    }
}
