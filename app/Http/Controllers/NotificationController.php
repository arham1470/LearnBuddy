<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a paginated list of in-app notifications for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        }

        $notifications = $query->latest()->paginate(15);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read with ownership validation (IDOR protected).
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a specific notification as read and redirect to its action URL (or notifications index).
     */
    public function readAndRedirect(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $actionUrl = $notification->data['action_url'] ?? null;

            if ($actionUrl) {
                // Ensure the redirect is internal to prevent open redirect vulnerabilities
                if (str_starts_with($actionUrl, '/') || str_starts_with($actionUrl, config('app.url')) || str_starts_with($actionUrl, url('/'))) {
                    return redirect($actionUrl);
                }
            }
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Get live unread notifications count for header badge updates.
     */
    public function unreadCount(): JsonResponse
    {
        $unreadCount = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }
}
