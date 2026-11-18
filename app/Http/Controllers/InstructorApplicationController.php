<?php

namespace App\Http\Controllers;

use App\Mail\SendEmailOtpMail;
use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\User;
use App\Rules\SriLankanPhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class InstructorApplicationController extends Controller
{
    /**
     * Show the "Become an Instructor" landing page with application form.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $existingApplication = null;

        if ($user) {
            $existingApplication = InstructorApplication::where('user_id', $user->id)
                ->latest()
                ->first();
        }

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view('pages.become-instructor', [
            'user' => $user,
            'existingApplication' => $existingApplication,
            'categories' => $categories,
        ]);
    }

    /**
     * Store an instructor application from a guest or existing logged-in user.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            return $this->storeForAuthenticatedUser($request, $user);
        }

        return $this->storeForGuest($request);
    }

    /**
     * Handle submission from an existing authenticated user.
     */
    protected function storeForAuthenticatedUser(Request $request, User $user): RedirectResponse
    {
        // Check if user already has an active pending application
        $pending = InstructorApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('become-instructor')
                ->with('status', 'You already have an instructor application pending admin review. Our team will contact you shortly.');
        }

        $validated = $request->validate([
            'headline' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:30'],
            'subject_areas' => ['required', 'array', 'min:1'],
            'subject_areas.*' => ['string', 'max:100'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'in:sinhala,english,tamil'],
            'phone' => ['nullable', 'string', new SriLankanPhone],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'sample_video_url' => ['nullable', 'url', 'max:500'],
        ]);

        if (! empty($validated['phone']) && empty($user->phone)) {
            $user->phone = SriLankanPhone::normalize($validated['phone']);
            $user->save();
        }

        InstructorApplication::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $validated['phone'] ? SriLankanPhone::normalize($validated['phone']) : $user->phone,
            'headline' => $validated['headline'],
            'bio' => $validated['bio'],
            'subject_areas' => $validated['subject_areas'],
            'languages' => $validated['languages'],
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'sample_video_url' => $validated['sample_video_url'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('become-instructor')
            ->with('success', 'Your instructor application has been submitted successfully! Our administrative team will review your qualifications and notify you via email.');
    }

    /**
     * Handle registration and instructor application for a guest visitor.
     */
    protected function storeForGuest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', new SriLankanPhone],
            'headline' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:30'],
            'subject_areas' => ['required', 'array', 'min:1'],
            'subject_areas.*' => ['string', 'max:100'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'in:sinhala,english,tamil'],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'sample_video_url' => ['nullable', 'url', 'max:500'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must agree to the LearnBuddy Instructor Terms & Conditions.',
        ]);

        $normalizedPhone = SriLankanPhone::normalize($validated['phone']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $normalizedPhone,
            'locale' => 'en',
            'timezone' => 'Asia/Colombo',
            'is_active' => false,
            'is_approved' => false,
        ]);

        $user->assignRole('instructor');

        InstructorApplication::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $normalizedPhone,
            'headline' => $validated['headline'],
            'bio' => $validated['bio'],
            'subject_areas' => $validated['subject_areas'],
            'languages' => $validated['languages'],
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'sample_video_url' => $validated['sample_video_url'] ?? null,
            'status' => 'pending',
        ]);

        $otpCode = $user->generateEmailOtp();

        try {
            Mail::to($user->email)->send(new SendEmailOtpMail($user, $otpCode));
        } catch (Throwable $e) {
            Log::error('Failed to send registration OTP email to instructor applicant: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.verify.show')
            ->with('status', 'Your instructor application account has been created! Please enter the 6-digit verification code sent to your email to submit your profile for admin review.');
    }
}
