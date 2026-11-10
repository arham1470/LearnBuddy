<header
    x-data="{ mobileNavOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="{ 'bg-white/90 backdrop-blur-md shadow-lb-sm': scrolled, 'bg-transparent': !scrolled }"
    class="sticky top-0 z-40 w-full transition-all duration-300 py-3 sm:py-4"
>
    <div class="max-w-[1140px] mx-auto px-4 sm:px-6 flex items-center justify-between">
        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded-lg">
            @if (file_exists(public_path('images/logo.png')))
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="LearnBuddy - Smart Learning Platform"
                    class="h-8 sm:h-9 w-auto object-contain"
                >
            @else
                <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-lb-black">
                    Learn<span class="text-lb-orange">Buddy</span>.
                </span>
            @endif
        </a>

        <!-- Desktop Navigation Pill (Reference UI Style) -->
        <nav
            class="hidden lg:flex items-center gap-8 bg-white px-8 py-2.5 rounded-full shadow-lb-sm border border-neutral-100/80 text-sm font-medium"
            aria-label="Main Navigation"
        >
            <a
                href="{{ url('/') }}"
                class="transition-colors hover:text-lb-orange {{ request()->is('/') ? 'text-lb-orange font-semibold' : 'text-neutral-700' }}"
            >
                Home
            </a>
            <a
                href="{{ url('/#courses') }}"
                class="transition-colors hover:text-lb-orange text-neutral-700"
            >
                Our Courses
            </a>
            <a
                href="{{ url('/#categories') }}"
                class="transition-colors hover:text-lb-orange text-neutral-700"
            >
                Categories
            </a>
            <a
                href="{{ url('/#about') }}"
                class="transition-colors hover:text-lb-orange text-neutral-700"
            >
                Who We Are
            </a>
            <a
                href="{{ url('/#faq') }}"
                class="transition-colors hover:text-lb-orange text-neutral-700"
            >
                FAQ
            </a>
            <a
                href="{{ url('/design-system') }}"
                class="transition-colors hover:text-lb-orange {{ request()->is('design-system') ? 'text-lb-orange font-semibold' : 'text-neutral-500 text-xs uppercase tracking-wider' }}"
            >
                Design System
            </a>
        </nav>

        <!-- Right Action Controls -->
        <div class="flex items-center gap-3 sm:gap-4">
            <!-- Search Trigger -->
            <button
                type="button"
                @click="$dispatch('open-modal', 'search-modal')"
                class="w-10 h-10 rounded-full flex items-center justify-center text-neutral-600 hover:text-lb-orange hover:bg-orange-50/60 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer"
                aria-label="Open search"
            >
                <i class="fas fa-search text-sm" aria-hidden="true"></i>
            </button>

            <!-- Authentication Actions (Desktop) -->
            @auth
                <div class="hidden sm:flex items-center gap-3">
                    <a
                        href="{{ route('profile.show', auth()->user()) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-neutral-100 hover:bg-orange-50 text-neutral-800 hover:text-lb-orange text-xs font-semibold transition"
                    >
                        <i class="fas fa-user-circle text-sm text-lb-orange" aria-hidden="true"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center p-2 rounded-full text-neutral-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer text-xs"
                            title="Sign out"
                            aria-label="Sign out"
                        >
                            <i class="fas fa-sign-out-alt text-sm" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="hidden sm:flex items-center gap-2">
                    <x-button
                        as="a"
                        href="{{ route('login') }}"
                        variant="dark"
                        size="sm"
                    >
                        Log in
                    </x-button>

                    <x-button
                        as="a"
                        href="{{ route('register') }}"
                        variant="primary"
                        size="sm"
                    >
                        Register
                    </x-button>
                </div>
            @endauth

            <!-- Mobile Hamburger Toggle Button -->
            <button
                type="button"
                @click="mobileNavOpen = !mobileNavOpen"
                class="lg:hidden w-11 h-11 rounded-full flex items-center justify-center text-neutral-800 hover:text-lb-orange hover:bg-neutral-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer"
                :aria-expanded="mobileNavOpen.toString()"
                aria-label="Toggle mobile menu"
            >
                <i class="fas" :class="mobileNavOpen ? 'fa-times text-lg' : 'fa-bars text-lg'" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer & Backdrop (Alpine.js) -->
    <div
        x-show="mobileNavOpen"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Mobile Menu"
    >
        <!-- Backdrop Overlay -->
        <div
            x-show="mobileNavOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"
            @click="mobileNavOpen = false"
        ></div>

        <!-- Slide-out Drawer Panel -->
        <div
            x-show="mobileNavOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-full max-w-xs sm:max-w-sm bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto z-10"
            @click.stop
        >
            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-6 border-b border-neutral-100">
                    <a href="{{ url('/') }}" @click="mobileNavOpen = false" class="flex items-center gap-2">
                        @if (file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="LearnBuddy" class="h-7 w-auto">
                        @else
                            <span class="text-lg font-extrabold text-lb-black">Learn<span class="text-lb-orange">Buddy</span>.</span>
                        @endif
                    </a>

                    <button
                        type="button"
                        @click="mobileNavOpen = false"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition"
                        aria-label="Close menu"
                    >
                        <i class="fas fa-times text-base" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Mobile Navigation Links (Minimum 44px Touch Targets) -->
                <nav class="flex flex-col py-6 space-y-1" aria-label="Mobile Navigation Links">
                    <a
                        href="{{ url('/') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-semibold transition {{ request()->is('/') ? 'bg-orange-50 text-lb-orange' : 'text-neutral-800 hover:bg-neutral-50' }}"
                    >
                        <span>Home</span>
                        <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ url('/#courses') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium text-neutral-800 hover:bg-neutral-50 transition"
                    >
                        <span>Our Courses</span>
                        <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ url('/#categories') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium text-neutral-800 hover:bg-neutral-50 transition"
                    >
                        <span>Categories</span>
                        <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ url('/#about') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium text-neutral-800 hover:bg-neutral-50 transition"
                    >
                        <span>Who We Are</span>
                        <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ url('/#faq') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium text-neutral-800 hover:bg-neutral-50 transition"
                    >
                        <span>FAQ</span>
                        <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ url('/design-system') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium transition {{ request()->is('design-system') ? 'bg-orange-50 text-lb-orange' : 'text-neutral-600 hover:bg-neutral-50' }}"
                    >
                        <span>Design System Tokens</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600">Dev</span>
                    </a>
                </nav>
            </div>

            <!-- Drawer Bottom Actions -->
            <div class="pt-6 border-t border-neutral-100 space-y-3">
                @auth
                    <div class="p-3 rounded-2xl bg-orange-50/60 border border-orange-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-user-circle text-xl text-lb-orange" aria-hidden="true"></i>
                            <div>
                                <p class="text-xs font-bold text-neutral-900">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-neutral-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <x-button
                        as="a"
                        href="{{ route('profile.show', auth()->user()) }}"
                        variant="dark"
                        class="w-full text-center"
                        @click="mobileNavOpen = false"
                    >
                        My Profile
                    </x-button>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 rounded-full border border-neutral-200 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <i class="fas fa-sign-out-alt text-xs" aria-hidden="true"></i>
                            <span>Sign out</span>
                        </button>
                    </form>
                @else
                    <x-button
                        as="a"
                        href="{{ route('login') }}"
                        variant="dark"
                        class="w-full text-center"
                        @click="mobileNavOpen = false"
                    >
                        Log in
                    </x-button>

                    <x-button
                        as="a"
                        href="{{ route('register') }}"
                        variant="primary"
                        class="w-full text-center"
                        @click="mobileNavOpen = false"
                    >
                        Register
                    </x-button>
                @endauth

                <x-button
                    as="a"
                    href="{{ url('/#courses') }}"
                    variant="outline"
                    class="w-full text-center"
                    @click="mobileNavOpen = false"
                >
                    Explore Courses
                </x-button>

                <!-- Sri Lanka Support info -->
                <div class="pt-2 text-center text-xs text-neutral-500">
                    <p class="flex items-center justify-center gap-1.5">
                        <i class="fas fa-phone text-lb-orange" aria-hidden="true"></i>
                        <span>+94 11 234 5678 (Colombo)</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>
