<?php

namespace App\Services\Notifications;

use App\Models\NotificationDeliveryLog;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationDispatcherService
{
    /**
     * Dispatch a notification to a user with strict deduplication check.
     */
    public function send(
        User $user,
        Notification $notification,
        ?string $deduplicationKey = null,
        string $eventType = 'general',
        string $channel = 'database'
    ): bool {
        if ($deduplicationKey) {
            if (NotificationDeliveryLog::hasBeenSent($deduplicationKey)) {
                Log::info("Notification skipped: duplicate key [{$deduplicationKey}] already delivered.", [
                    'user_id' => $user->id,
                    'event_type' => $eventType,
                ]);

                return false;
            }

            $record = NotificationDeliveryLog::recordDelivery(
                deduplicationKey: $deduplicationKey,
                eventType: $eventType,
                userId: $user->id,
                channel: $channel,
                status: 'sent'
            );

            if (! $record) {
                // Concurrency race: another process just inserted the key
                return false;
            }
        }

        try {
            $user->notify($notification);

            return true;
        } catch (\Throwable $e) {
            Log::error("Error delivering notification [{$eventType}] to user [{$user->id}]: ".$e->getMessage());

            if ($deduplicationKey) {
                NotificationDeliveryLog::where('deduplication_key', $deduplicationKey)
                    ->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
            }

            return false;
        }
    }

    /**
     * Dispatch notifications to multiple users with per-user deduplication.
     *
     * @param  Collection<int, User>|array<User>  $users
     */
    public function sendToUsers(
        Collection|array $users,
        callable $notificationFactory,
        callable $dedupKeyGenerator,
        string $eventType = 'batch_event'
    ): int {
        $sentCount = 0;

        foreach ($users as $user) {
            $dedupKey = $dedupKeyGenerator($user);
            $notification = $notificationFactory($user);

            if ($this->send($user, $notification, $dedupKey, $eventType)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }
}
