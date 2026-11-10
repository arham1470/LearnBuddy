<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     * Prevents user enumeration by showing the same generic response whether the email exists or not.
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        // Broker attempts to send the reset link if the user exists
        Password::broker()->sendResetLink(
            $request->only('email')
        );

        // Security: Always return the identical message to prevent user enumeration
        return back()->with('status', __('If an account with that email exists, we have sent a password reset link.'));
    }
}
