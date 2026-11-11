<?php

namespace App\Http\Controllers;

use App\Models\UserNotificationPreference;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    /**
     * Display the notification preferences settings screen.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $preferences = UserNotificationPreference::forUser($user);

        return view('settings.notifications', compact('preferences', 'user'));
    }

    /**
     * Update notification preferences for the authenticated user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $preferences = UserNotificationPreference::forUser($user);

        $validated = $request->validate([
            'email_enrollment' => ['nullable', 'boolean'],
            'email_payment' => ['nullable', 'boolean'],
            'email_live_class' => ['nullable', 'boolean'],
            'email_assignments' => ['nullable', 'boolean'],
            'email_quizzes' => ['nullable', 'boolean'],
            'email_announcements' => ['nullable', 'boolean'],
            'in_app_notifications' => ['nullable', 'boolean'],
            'push_notifications' => ['nullable', 'boolean'],
        ]);

        $preferences->update([
            'email_enrollment' => (bool) $request->has('email_enrollment'),
            'email_payment' => (bool) $request->has('email_payment'),
            'email_live_class' => (bool) $request->has('email_live_class'),
            'email_assignments' => (bool) $request->has('email_assignments'),
            'email_quizzes' => (bool) $request->has('email_quizzes'),
            'email_announcements' => (bool) $request->has('email_announcements'),
            'in_app_notifications' => (bool) $request->has('in_app_notifications'),
            'push_notifications' => (bool) $request->has('push_notifications'),
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
