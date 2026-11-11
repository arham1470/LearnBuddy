<?php

namespace App\Services\Notifications\Push;

use App\Models\User;

interface PushNotificationProviderInterface
{
    /**
     * Get the identifier name of the push provider.
     */
    public function getProviderName(): string;

    /**
     * Determine whether the provider credentials are fully configured.
     */
    public function isConfigured(): bool;

    /**
     * Send a web or mobile push notification to the user.
     *
     * @param  array<string, mixed>  $data
     */
    public function send(User $user, string $title, string $body, array $data = []): bool;
}
