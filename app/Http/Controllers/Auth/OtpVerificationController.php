<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendEmailOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class OtpVerificationController extends Controller
{
    /**
     * Show the 6-digit OTP verification screen.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $userId = session('otp_user_id');

        if (! $userId) {
            return redirect()->route('login')->with('status', __('Please sign in or register to verify your account.'));
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget('otp_user_id');
            return redirect()->route('register');
        }

        // If email is already verified
        if ($user->hasVerifiedEmail()) {
            if ($user->isApproved()) {
                session()->forget('otp_user_id');
                return redirect()->route('login')->with('status', __('Your email is already verified and your account is active. Please sign in.'));
            }

            return redirect()->route('approval.notice');
        }

        return view('auth.verify-otp', [
            'user' => $user,
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    /**
     * Verify the entered 6-digit OTP.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size' => 'The verification code must be exactly 6 digits.',
            'otp.regex' => 'The verification code must contain numbers only.',
        ]);

        $userId = session('otp_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget('otp_user_id');
            return redirect()->route('register');
        }

        if (! $user->verifyEmailOtp($request->otp)) {
            throw ValidationException::withMessages([
                'otp' => __('The verification code entered is invalid or has expired. Please check your inbox or request a new code.'),
            ]);
        }

        // Session cleanup and keep name for confirmation display
        session()->forget('otp_user_id');
        session(['verified_user_name' => $user->name]);

        return redirect()->route('approval.notice');
    }

    /**
     * Resend a fresh 6-digit OTP to user's email.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('otp_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget('otp_user_id');
            return redirect()->route('register');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('approval.notice');
        }

        $otpCode = $user->generateEmailOtp();

        try {
            Mail::to($user->email)->send(new SendEmailOtpMail($user, $otpCode));
        } catch (Throwable $e) {
            Log::error('Failed to resend OTP email: '.$e->getMessage(), [
                'user_id' => $user->id,
            ]);
        }

        return back()->with('status', __('A new 6-digit verification code has been sent to your email address.'));
    }

    /**
     * Display the pending administrator approval notice.
     */
    public function pendingApproval(): View
    {
        return view('auth.pending-approval');
    }

    /**
     * Mask email for secure UI presentation (e.g. s***t@learnbuddy.lk).
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $length = strlen($name);
        if ($length <= 2) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', max(1, $length - 2)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }
}
