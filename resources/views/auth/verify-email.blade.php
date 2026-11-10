<x-layouts.public title="Verify Your Email - LearnBuddy" description="Verify your email address to activate your full LearnBuddy account.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="w-full max-w-md">
            <!-- Brand & Heading -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-orange-100 text-lb-orange flex items-center justify-center text-2xl mb-4 shadow-sm">
                    <i class="fas fa-envelope-open-text" aria-hidden="true"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Verify your email</h1>
                <p class="mt-2 text-sm text-neutral-600">
                    Thanks for joining LearnBuddy! Please click on the verification link we sent to your inbox to activate your account.
                </p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 text-center">
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3 text-left" role="status">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span>A new verification link has been sent to your email address. Please check your inbox and spam folder.</span>
                    </div>
                @endif

                <div class="space-y-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-lb-orange cursor-pointer"
                        >
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                            <span>Resend verification email</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-6 rounded-full font-semibold text-neutral-600 hover:text-lb-black hover:bg-neutral-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400 cursor-pointer text-sm"
                        >
                            <i class="fas fa-sign-out-alt text-xs" aria-hidden="true"></i>
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>

                <div class="mt-6 pt-6 border-t border-neutral-100 text-xs text-neutral-500">
                    Need help? Contact support at <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
