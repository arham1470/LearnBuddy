<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the public support contact form.
     */
    public function create(Request $request): View
    {
        $user = auth()->user();
        $defaultSubject = $request->query('subject', '');
        $defaultCategory = $request->query('category', 'general');

        return view('support.create', compact('user', 'defaultSubject', 'defaultCategory'));
    }

    /**
     * Store a new support message from a user or visitor.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'in:general,account,billing,courses,technical'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $supportMessage = SupportMessage::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'status' => SupportMessage::STATUS_PENDING,
            'ip_address' => $request->ip(),
        ]);

        $successMessage = __('Thank you for contacting LearnBuddy Support! Your ticket #:id has been submitted. Our team will review your message and reply directly to your email address.', ['id' => $supportMessage->id]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'ticket_id' => $supportMessage->id,
            ]);
        }

        return redirect()->back()->with('support_success', $successMessage);
    }
}
