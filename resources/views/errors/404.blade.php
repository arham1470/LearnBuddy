<x-layouts.public title="404 - Page Not Found · LearnBuddy" description="The page you are looking for does not exist on LearnBuddy.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="w-full max-w-xl text-center">
            <!-- Graphical Icon Badge -->
            <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 rounded-3xl bg-orange-100/70"></div>
                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-tr from-lb-orange to-amber-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-orange-500/20">
                    <i class="fas fa-compass" aria-hidden="true"></i>
                </div>
            </div>

            <!-- Error Code & Header -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fas fa-exclamation-triangle text-xs" aria-hidden="true"></i>
                <span>404 &bull; Page Not Found</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-lb-black tracking-tight">
                We couldn't find that page
            </h1>

            <p class="mt-3 text-sm sm:text-base text-neutral-600 max-w-md mx-auto leading-relaxed">
                The link you followed may be broken, or the course, batch, or resource may have been relocated or removed.
            </p>

            <!-- Card Actions -->
            <div class="mt-8 bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <a
                        href="{{ route('home') }}"
                        class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-xs cursor-pointer"
                    >
                        <i class="fas fa-home text-xs" aria-hidden="true"></i>
                        <span>Return Home</span>
                    </a>

                    <a
                        href="{{ route('courses.index') }}"
                        class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition text-xs cursor-pointer"
                    >
                        <i class="fas fa-graduation-cap text-xs text-neutral-500" aria-hidden="true"></i>
                        <span>Explore Courses</span>
                    </a>

                    <button
                        type="button"
                        onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('home') }}'"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 py-3 px-4 rounded-full border border-neutral-200 hover:bg-neutral-50 text-neutral-600 text-xs font-semibold transition cursor-pointer"
                        title="Go back to previous page"
                    >
                        <i class="fas fa-arrow-left text-[11px]" aria-hidden="true"></i>
                        <span class="sm:hidden">Go Back</span>
                    </button>
                </div>
            </div>

            <!-- Footer Support Contact -->
            <p class="mt-8 text-xs text-neutral-400">
                Need help finding something? <a href="{{ route('courses.index') }}" class="text-lb-orange hover:underline font-medium">Search courses</a> or contact <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
            </p>
        </div>
    </section>
</x-layouts.public>
