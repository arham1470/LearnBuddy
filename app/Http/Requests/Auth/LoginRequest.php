<?php

namespace App\Http\Requests\Auth;

use App\Mail\SendEmailOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // 1. Enforce Email Verification via OTP
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            // Refresh or generate OTP if expired or missing
            if (empty($user->email_otp) || empty($user->email_otp_expires_at) || now()->isAfter($user->email_otp_expires_at)) {
                $otpCode = $user->generateEmailOtp();
                try {
                    Mail::to($user->email)->send(new SendEmailOtpMail($user, $otpCode));
                } catch (Throwable $e) {
                    Log::error('Failed to send login OTP email: '.$e->getMessage(), ['user_id' => $user->id]);
                }
            }

            $this->session()->put('otp_user_id', $user->id);

            throw new HttpResponseException(
                redirect()->route('otp.verify.show')->with('status', __('Please verify your email address. A 6-digit code has been sent to your inbox.'))
            );
        }

        // 2. Enforce Administrator Approval
        if (! $user->isApproved()) {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();
            $this->session()->flash('account_pending_approval', true);

            throw ValidationException::withMessages([
                'email' => __('Your account has been verified, but is awaiting administrator approval. You will receive an email once approved.'),
            ]);
        }

        // 3. Enforce Account Active Status
        if (! $user->is_active) {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();
            $this->session()->flash('account_deactivated', true);

            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated. Please contact LearnBuddy support.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
