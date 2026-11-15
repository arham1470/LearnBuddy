<x-layouts.public title="403 - Access Restricted · LearnBuddy" description="You do not have permission to access this resource on LearnBuddy.">
    <section class="min-h-[calc(100vh-140px)] flex items-center justify-center py-12 sm:py-16 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="w-full max-w-xl text-center">
            <!-- Graphical Icon Badge -->
            <div class="relative w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 rounded-3xl bg-amber-100/80 animate-pulse"></div>
                <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center text-3xl shadow-lg shadow-orange-500/20">
                    <i class="fas fa-lock" aria-hidden="true"></i>
                </div>
                <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-bold border-2 border-white shadow-sm" title="Forbidden">
                    <i class="fas fa-ban" aria-hidden="true"></i>
                </div>
            </div>

            <!-- Error Code & Header -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fas fa-shield-alt text-xs" aria-hidden="true"></i>
                <span>403 &bull; Access Restricted</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-lb-black tracking-tight">
                This action is unauthorized
            </h1>

            <p class="mt-3 text-sm sm:text-base text-neutral-600 max-w-md mx-auto leading-relaxed">
                {{ !empty($exception->getMessage()) ? $exception->getMessage() : 'You do not have the required administrative or faculty privileges to access this restricted page.' }}
            </p>

            <!-- Card Container -->
            <div class="mt-8 bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 text-left">
                @auth
                    <!-- Active User Identity Card -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 flex items-center justify-between gap-3 mb-6">
                        <div class="flex items-center gap-3 min-w-0">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-lb-orange/10 text-lb-orange font-bold text-sm flex items-center justify-center shrink-0 border border-orange-200/60">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-neutral-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-neutral-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            @if (auth()->user()->isAdmin())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700">Admin</span>
                            @elseif (auth()->user()->isInstructor())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700">Instructor</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">Student</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-orange-50/70 border border-orange-100 text-xs text-neutral-600 flex items-start gap-2.5 mb-6">
                        <i class="fas fa-info-circle text-lb-orange text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                        <span>
                            Need faculty or administrator access? Contact your LearnBuddy campus coordinator or switch to an authorized faculty account.
                        </span>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-100 text-xs text-neutral-700 flex items-start gap-3 mb-6">
                        <i class="fas fa-user-lock text-lb-orange text-base shrink-0 mt-0.5" aria-hidden="true"></i>
                        <div>
                            <p class="font-bold text-neutral-900">Are you an administrator or faculty member?</p>
                            <p class="mt-0.5 text-neutral-600">Please sign in with your staff credentials to access course management and administration tools.</p>
                        </div>
                    </div>
                @endauth

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    @auth
                        <a
                            href="{{ route('student.dashboard') }}"
                            class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-xs cursor-pointer"
                        >
                            <i class="fas fa-graduation-cap text-xs" aria-hidden="true"></i>
                            <span>My Dashboard</span>
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-sm text-xs cursor-pointer"
                        >
                            <i class="fas fa-sign-in-alt text-xs" aria-hidden="true"></i>
                            <span>Sign In</span>
                        </a>
                    @endauth

                    <a
                        href="{{ route('courses.index') }}"
                        class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 py-3 px-6 rounded-full font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition text-xs cursor-pointer"
                    >
                        <i class="fas fa-search text-xs text-neutral-500" aria-hidden="true"></i>
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

                @auth
                    <!-- Switch account option -->
                    <div class="mt-5 pt-5 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral-500">
                        <span>Signed in with wrong account?</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="font-semibold text-rose-600 hover:underline cursor-pointer"
                            >
                                Sign out & switch account
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- Footer Support Contact -->
            <p class="mt-8 text-xs text-neutral-400">
                Need assistance? Contact LearnBuddy support at <a href="mailto:support@learnbuddy.lk" class="text-lb-orange hover:underline font-medium">support@learnbuddy.lk</a>
            </p>
        </div>
    </section>
</x-layouts.public>
