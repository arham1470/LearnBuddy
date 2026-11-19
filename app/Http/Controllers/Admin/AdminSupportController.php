<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupportController extends Controller
{
    /**
     * Display a listing of support messages with status filtering and search.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'all'); // 'all', 'pending', 'replied'
        $category = $request->query('category', 'all');
        $search = $request->query('search', '');

        $query = SupportMessage::query()
            ->with(['user', 'repliedBy'])
            ->latest('created_at');

        if ($tab === 'pending') {
            $query->pending();
        } elseif ($tab === 'replied') {
            $query->replied();
        }

        if ($category !== 'all' && array_key_exists($category, SupportMessage::CATEGORIES)) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $totalCount = SupportMessage::count();
        $pendingCount = SupportMessage::pending()->count();
        $repliedCount = SupportMessage::replied()->count();

        return view('admin.support.index', compact(
            'messages',
            'tab',
            'category',
            'search',
            'totalCount',
            'pendingCount',
            'repliedCount'
        ));
    }

    /**
     * Record the admin's reply, mark the ticket as replied, and redirect to Gmail Compose.
     */
    public function reply(Request $request, SupportMessage $supportMessage): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $replyText = $validated['reply'];

        $supportMessage->update([
            'admin_reply' => $replyText,
            'status' => SupportMessage::STATUS_REPLIED,
            'replied_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        $gmailUrl = $supportMessage->buildGmailComposeUrl($replyText);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Support ticket marked as replied.'),
                'gmail_url' => $gmailUrl,
                'support_message' => $supportMessage->fresh(['repliedBy']),
            ]);
        }

        return redirect()->away($gmailUrl);
    }

    /**
     * Toggle or manually mark a ticket status without redirecting to Gmail.
     */
    public function toggleStatus(Request $request, SupportMessage $supportMessage): RedirectResponse|JsonResponse
    {
        $newStatus = $supportMessage->isPending() ? SupportMessage::STATUS_REPLIED : SupportMessage::STATUS_PENDING;

        $supportMessage->update([
            'status' => $newStatus,
            'replied_at' => $newStatus === SupportMessage::STATUS_REPLIED ? now() : null,
            'replied_by' => $newStatus === SupportMessage::STATUS_REPLIED ? auth()->id() : null,
        ]);

        $message = $newStatus === SupportMessage::STATUS_REPLIED
            ? __('Ticket marked as replied.')
            : __('Ticket reopened as pending.');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete a support message from the database.
     */
    public function destroy(SupportMessage $supportMessage): RedirectResponse
    {
        $supportMessage->delete();

        return redirect()->back()->with('success', __('Support message deleted successfully.'));
    }
}
