<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class RegistrationVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address · LearnBuddy Sri Lanka')
            ->greeting("Welcome, {$notifiable->name}!")
            ->line('Welcome to LearnBuddy! Please verify your email address to activate your full student access.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create a LearnBuddy account, no further action is required.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'registration_verification',
            'title' => 'Verify Your Email Address',
            'body' => 'Welcome to LearnBuddy! Please verify your email to unlock all features.',
            'icon' => 'fas fa-envelope',
            'action_url' => route('verification.notice'),
            'metadata' => [
                'user_id' => $notifiable->id,
            ],
        ];
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
