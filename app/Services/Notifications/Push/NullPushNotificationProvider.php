<?php

namespace App\Services\Notifications\Push;

use App\Models\User;

class NullPushNotificationProvider implements PushNotificationProviderInterface
{
    public function getProviderName(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function send(User $user, string $title, string $body, array $data = []): bool
    {
        // Safe testing no-op
        return true;
    }
}
