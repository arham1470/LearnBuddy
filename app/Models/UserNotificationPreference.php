<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_enrollment',
        'email_payment',
        'email_live_class',
        'email_assignments',
        'email_quizzes',
        'email_announcements',
        'in_app_notifications',
        'push_notifications',
    ];

    protected function casts(): array
    {
        return [
            'email_enrollment' => 'boolean',
            'email_payment' => 'boolean',
            'email_live_class' => 'boolean',
            'email_assignments' => 'boolean',
            'email_quizzes' => 'boolean',
            'email_announcements' => 'boolean',
            'in_app_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create notification preferences for a given user.
     */
    public static function forUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enrollment' => true,
                'email_payment' => true,
                'email_live_class' => true,
                'email_assignments' => true,
                'email_quizzes' => true,
                'email_announcements' => true,
                'in_app_notifications' => true,
                'push_notifications' => false,
            ]
        );
    }

    /**
     * Check if user allows notification for category and channel.
     */
    public function canReceive(string $category, string $channel = 'mail'): bool
    {
        if ($channel === 'database') {
            return $this->in_app_notifications;
        }

        if ($channel === 'push') {
            return $this->push_notifications;
        }

        // Email channel
        return match ($category) {
            'enrollment' => $this->email_enrollment,
            'payment' => $this->email_payment,
            'live_class' => $this->email_live_class,
            'assignment' => $this->email_assignments,
            'quiz' => $this->email_quizzes,
            'announcement' => $this->email_announcements,
            default => true,
        };
    }
}
