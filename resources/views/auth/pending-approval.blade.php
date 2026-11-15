<x-layouts.public title="Account Pending Approval - LearnBuddy" description="Your LearnBuddy account is awaiting administrative approval.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-lg">
            <!-- Brand & Heading -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mb-4 shadow-sm">
                    <i class="fas fa-user-clock" aria-hidden="true"></i>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold tracking-wide uppercase mb-3">
                    <i class="fas fa-check-circle text-xs" aria-hidden="true"></i> Email Verified Successfully
                </div>
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Account Pending Approval</h1>
                <p class="mt-2 text-sm text-neutral-600 max-w-sm mx-auto">
                    Welcome! Your email address has been verified. Your registration is now awaiting review by a LearnBuddy administrator.
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <!-- Status Timeline / Steps -->
                <div class="space-y-4 mb-8">
                    <!-- Step 1: Complete -->
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            <i class="fas fa-check" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-emerald-950">1. Email Verified</h2>
                            <p class="text-xs text-emerald-700 mt-0.5">Your email address has been confirmed with your 6-digit OTP code.</p>
                        </div>
                    </div>

                    <!-- Step 2: In Progress -->
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-amber-950">2. Administrator Review (Current)</h2>
                            <p class="text-xs text-amber-800 mt-0.5">Our administration team verifies all incoming student accounts to ensure a trusted learning community.</p>
                        </div>
                    </div>

                    <!-- Step 3: Pending -->
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-neutral-50 border border-neutral-100">
                        <div class="w-8 h-8 rounded-full bg-neutral-200 text-neutral-500 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            <i class="fas fa-envelope" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-neutral-700">3. Notification & Sign In</h2>
                            <p class="text-xs text-neutral-500 mt-0.5">You will receive an automated email as soon as your account is approved. You can then sign in and start learning.</p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-100 text-xs text-neutral-700 flex items-start gap-3 mb-6">
                    <i class="fas fa-info-circle text-lb-orange text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <span class="font-semibold text-neutral-900">How long does approval take?</span>
                        <p class="mt-0.5 text-neutral-600">Approvals are typically processed within a few hours during business days. Please keep an eye on your email inbox and spam folder.</p>
                    </div>
                </div>

                <!-- Action Button -->
                <div>
                    <a
                        href="{{ route('login') }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-sm"
                    >
                        <i class="fas fa-sign-in-alt text-xs" aria-hidden="true"></i>
                        <span>Return to Sign In</span>
                    </a>
                </div>

                <div class="mt-6 pt-6 border-t border-neutral-100 text-center text-xs text-neutral-500">
                    Questions or urgent inquiries? Reach us at <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
