<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\SendEmailOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->normalizedPhone(),
            'password' => $request->password,
            'locale' => 'en',
            'timezone' => 'Asia/Colombo',
            'is_active' => false,
            'is_approved' => false,
        ]);

        $user->assignRole('student');

        $otpCode = $user->generateEmailOtp();

        try {
            Mail::to($user->email)->send(new SendEmailOtpMail($user, $otpCode));
        } catch (Throwable $e) {
            Log::error('Failed to dispatch registration OTP email: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.verify.show')->with('status', __('A 6-digit verification code has been sent to your email address.'));
    }
}
