<x-layouts.public title="419 - Page Expired · LearnBuddy" description="Your session has expired. Please refresh the page and try again.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="w-full max-w-xl text-center">
            <!-- Graphical Icon Badge -->
            <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 rounded-3xl bg-amber-100/70"></div>
                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-orange-500/20">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                </div>
            </div>

            <!-- Error Code & Header -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fas fa-hourglass-end text-xs" aria-hidden="true"></i>
                <span>419 &bull; Page Expired</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-lb-black tracking-tight">
                Your session has timed out
            </h1>

            <p class="mt-3 text-sm sm:text-base text-neutral-600 max-w-md mx-auto leading-relaxed">
                For your security, forms expire after a period of inactivity. Please refresh the page and submit your request again.
            </p>

            <!-- Card Actions -->
            <div class="mt-8 bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button
                        type="button"
                        onclick="window.location.reload();"
                        class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-xs cursor-pointer"
                    >
                        <i class="fas fa-redo-alt text-xs" aria-hidden="true"></i>
                        <span>Refresh Page</span>
                    </button>

                    <a
                        href="{{ route('login') }}"
                        class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition text-xs cursor-pointer"
                    >
                        <i class="fas fa-sign-in-alt text-xs text-neutral-500" aria-hidden="true"></i>
                        <span>Sign In</span>
                    </a>
                </div>
            </div>

            <!-- Footer Support Contact -->
            <p class="mt-8 text-xs text-neutral-400">
                Having repeated issues? Contact <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
            </p>
        </div>
    </section>
</x-layouts.public>
