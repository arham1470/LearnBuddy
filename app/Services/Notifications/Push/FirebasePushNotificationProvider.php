<?php

namespace App\Services\Notifications\Push;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class FirebasePushNotificationProvider implements PushNotificationProviderInterface
{
    protected ?string $projectId;

    protected ?string $serviceAccountKey;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->serviceAccountKey = config('services.firebase.credentials');
    }

    public function getProviderName(): string
    {
        return 'firebase';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->projectId) && ! empty($this->serviceAccountKey);
    }

    public function send(User $user, string $title, string $body, array $data = []): bool
    {
        if (! $this->isConfigured()) {
            Log::info('Firebase Cloud Messaging (FCM) is unconfigured. Push notification skipped.', [
                'user_id' => $user->id,
                'title' => $title,
            ]);

            return false;
        }

        // When configured with a Service Account and device token
        // In this phase, without live credentials, we report clearly unconfigured
        return false;
    }
}
