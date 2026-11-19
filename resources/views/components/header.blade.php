<header
    x-data="{ mobileNavOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="{ 'bg-white/90 backdrop-blur-md shadow-lb-sm': scrolled, 'bg-transparent': !scrolled }"
    class="sticky top-0 z-40 w-full transition-all duration-300 py-3 sm:py-4"
>
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
        <!-- Brand Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded-lg shrink-0">
            @if (file_exists(public_path('images/logo.png')))
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="LearnBuddy - Smart Learning Platform"
                    class="h-9 sm:h-11 w-auto object-contain"
                >
            @else
                <span class="text-2xl sm:text-[26px] font-extrabold tracking-tight text-lb-black whitespace-nowrap">
                    Learn<span class="text-lb-orange">Buddy</span>.
                </span>
            @endif
        </a>

        <!-- Desktop Navigation Pill (Reference UI Style) -->
        <nav
            class="hidden lg:flex items-center gap-4 xl:gap-7 bg-white px-5 xl:px-7 py-2.5 rounded-full shadow-lb-sm border border-neutral-100/80 text-xs xl:text-sm font-medium whitespace-nowrap shrink-0"
            aria-label="Main Navigation"
        >
            <a
                href="{{ url('/') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap {{ request()->is('/') ? 'text-lb-orange font-semibold' : 'text-neutral-700' }}"
            >
                Home
            </a>
            <a
                href="{{ route('courses.index') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap {{ request()->routeIs('courses.*') ? 'text-lb-orange font-semibold' : 'text-neutral-700' }}"
            >
                Our Courses
            </a>
            <a
                href="{{ url('/#categories') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap text-neutral-700"
            >
                Categories
            </a>
            <a
                href="{{ url('/#about') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap text-neutral-700"
            >
                Who We Are
            </a>
            <a
                href="{{ url('/#faq') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap text-neutral-700"
            >
                FAQ
            </a>
            <a
                href="{{ route('become-instructor') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap {{ request()->routeIs('become-instructor*') ? 'text-lb-orange font-semibold' : 'text-neutral-700' }}"
            >
                Teach on LearnBuddy
            </a>
            <a
                href="{{ url('/design-system') }}"
                class="transition-colors hover:text-lb-orange whitespace-nowrap {{ request()->is('design-system') ? 'text-lb-orange font-semibold' : 'hidden xl:inline-block text-neutral-400 hover:text-neutral-600 text-xs uppercase tracking-wider' }}"
            >
                Design System
            </a>
        </nav>

        <!-- Right Action Controls -->
        <div class="flex items-center gap-1.5 sm:gap-2 lg:gap-2.5 shrink-0">
            <!-- Search Trigger -->
            <div class="relative group shrink-0">
                <button
                    type="button"
                    @click="$dispatch('open-modal', 'search-modal')"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-neutral-600 hover:text-lb-orange bg-neutral-100 hover:bg-orange-50/80 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer shrink-0"
                    aria-label="Open search"
                >
                    <i class="fas fa-search text-sm" aria-hidden="true"></i>
                </button>
                <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                    Search courses
                    <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                </div>
            </div>

            <!-- Authentication Actions (Desktop) -->
            @auth
                <div class="hidden sm:flex items-center gap-1.5 sm:gap-2 lg:gap-2.5 shrink-0">
                    @if (auth()->user()->isAdmin())
                        <!-- Admin Dashboard Icon -->
                        <div class="relative group shrink-0">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition shrink-0 {{ request()->routeIs('admin.*') || request()->is('admin*') ? 'bg-orange-100 text-lb-orange ring-2 ring-lb-orange/30' : 'bg-neutral-100 hover:bg-orange-50 text-neutral-700 hover:text-lb-orange' }}"
                                aria-label="Admin Dashboard"
                            >
                                <i class="fas fa-chart-line text-sm text-lb-orange" aria-hidden="true"></i>
                            </a>
                            <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                                Admin Dashboard
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                            </div>
                        </div>

                        <!-- Course Management Icon for Admin -->
                        <div class="relative group shrink-0">
                            <a
                                href="{{ route('management.courses.index') }}"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition shrink-0 {{ request()->is('management*') ? 'bg-orange-100 text-lb-orange ring-2 ring-lb-orange/30' : 'bg-neutral-100 hover:bg-orange-50 text-neutral-700 hover:text-lb-orange' }}"
                                aria-label="Course Management"
                            >
                                <i class="fas fa-layer-group text-sm text-lb-orange" aria-hidden="true"></i>
                            </a>
                            <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                                Course Management
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                            </div>
                        </div>
                    @elseif (auth()->user()->isInstructor())
                        <!-- Course Management Icon for Instructor -->
                        <div class="relative group shrink-0">
                            <a
                                href="{{ route('management.courses.index') }}"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition shrink-0 {{ request()->is('management*') ? 'bg-orange-100 text-lb-orange ring-2 ring-lb-orange/30' : 'bg-neutral-100 hover:bg-orange-50 text-neutral-700 hover:text-lb-orange' }}"
                                aria-label="Course Management"
                            >
                                <i class="fas fa-chalkboard-teacher text-sm text-lb-orange" aria-hidden="true"></i>
                            </a>
                            <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                                Course Management
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                            </div>
                        </div>
                    @else
                        <!-- My Learning Icon for Student -->
                        <div class="relative group shrink-0">
                            <a
                                href="{{ route('student.dashboard') }}"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition shrink-0 {{ request()->routeIs('student.dashboard') ? 'bg-orange-100 text-lb-orange ring-2 ring-lb-orange/30' : 'bg-neutral-100 hover:bg-orange-50 text-neutral-700 hover:text-lb-orange' }}"
                                aria-label="My Learning"
                            >
                                <i class="fas fa-graduation-cap text-sm text-lb-orange" aria-hidden="true"></i>
                            </a>
                            <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                                My Learning
                                <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                            </div>
                        </div>
                    @endif

                    <!-- In-App Notification Bell & Dropdown Menu -->
                    <div
                        x-data="{ notificationOpen: false }"
                        @click.outside="notificationOpen = false"
                        @keydown.escape.window="notificationOpen = false"
                        class="relative shrink-0"
                    >
                        @php
                            $headerUser = auth()->user();
                            $headerNotifications = $headerUser ? $headerUser->notifications()->latest()->take(3)->get() : collect();
                            $unreadHeaderCount = $headerUser ? $headerUser->unreadNotifications()->count() : 0;
                        @endphp
                        <button
                            type="button"
                            @click="notificationOpen = !notificationOpen"
                            class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-neutral-100 hover:bg-orange-50 text-neutral-700 hover:text-lb-orange transition shrink-0 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            :class="{ 'bg-orange-50 text-lb-orange ring-2 ring-orange-200': notificationOpen }"
                            aria-label="Notifications"
                            :aria-expanded="notificationOpen.toString()"
                        >
                            <i class="fas fa-bell text-sm" aria-hidden="true"></i>
                            @if ($unreadHeaderCount > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-lb-orange text-[9px] font-black text-white shadow-xs">
                                    {{ $unreadHeaderCount > 9 ? '9+' : $unreadHeaderCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Panel -->
                        <div
                            x-show="notificationOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-neutral-200/80 py-0 z-50 overflow-hidden"
                            role="menu"
                        >
                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/70">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-neutral-900">Notifications</span>
                                    @if ($unreadHeaderCount > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-orange-100 text-lb-orange text-[10px] font-extrabold">
                                            {{ $unreadHeaderCount }} unread
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-neutral-400 font-medium">Recent 3</span>
                            </div>

                            <!-- Notifications List -->
                            @if ($headerNotifications->isEmpty())
                                <div class="p-6 text-center space-y-2">
                                    <div class="w-10 h-10 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto text-sm">
                                        <i class="fas fa-bell-slash"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-neutral-800">No notifications</p>
                                    <p class="text-[11px] text-neutral-400">You have no recent notifications.</p>
                                </div>
                            @else
                                <div class="divide-y divide-neutral-100 max-h-[340px] overflow-y-auto">
                                    @foreach ($headerNotifications as $notification)
                                        @php
                                            $data = $notification->data;
                                            $isUnread = is_null($notification->read_at);
                                            $icon = $data['icon'] ?? 'fas fa-bell';
                                            $title = $data['title'] ?? 'Notification';
                                            $body = $data['body'] ?? '';
                                            $targetUrl = route('notifications.go', $notification->id);
                                            $timeAgo = $notification->created_at->diffForHumans();
                                        @endphp
                                        <a
                                            href="{{ $targetUrl }}"
                                            class="flex items-start gap-3 p-3.5 transition {{ $isUnread ? 'bg-orange-50/40 hover:bg-orange-50/70' : 'hover:bg-neutral-50' }} text-left block group"
                                            role="menuitem"
                                        >
                                            <div class="relative shrink-0 mt-0.5">
                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs {{ $isUnread ? 'bg-orange-100 text-lb-orange' : 'bg-neutral-100 text-neutral-500' }}">
                                                    <i class="{{ $icon }}"></i>
                                                </div>
                                                @if ($isUnread)
                                                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-lb-orange ring-2 ring-white"></span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-1.5 mb-0.5">
                                                    <p class="text-xs font-bold text-neutral-900 group-hover:text-lb-orange transition truncate {{ $isUnread ? 'font-black' : '' }}">
                                                        {{ $title }}
                                                    </p>
                                                    <span class="text-[10px] text-neutral-400 shrink-0 font-medium whitespace-nowrap">
                                                        {{ $timeAgo }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-neutral-600 line-clamp-2 leading-relaxed">
                                                    {{ $body }}
                                                </p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="p-2 border-t border-neutral-100 bg-neutral-50/70">
                                <a
                                    href="{{ route('notifications.index') }}"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-white hover:bg-orange-50 border border-neutral-200 hover:border-orange-200 text-xs font-bold text-neutral-700 hover:text-lb-orange transition shadow-2xs group"
                                    role="menuitem"
                                >
                                    <span>View all notifications</span>
                                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Icon & Dropdown Menu -->
                    <div
                        x-data="{ userMenuOpen: false }"
                        @click.outside="userMenuOpen = false"
                        @keydown.escape.window="userMenuOpen = false"
                        class="relative group shrink-0"
                    >
                        <button
                            type="button"
                            @click="userMenuOpen = !userMenuOpen"
                            :aria-expanded="userMenuOpen.toString()"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-neutral-100 hover:bg-orange-50 text-neutral-800 hover:text-lb-orange transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer border border-transparent hover:border-orange-200 shrink-0"
                            aria-label="User account menu"
                        >
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-lb-orange text-white flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            @endif
                        </button>

                        <!-- Tooltip on hover (hidden when menu open) -->
                        <div
                            x-show="!userMenuOpen"
                            class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50"
                        >
                            {{ auth()->user()->name }}
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                        </div>

                        <!-- Dropdown Menu Card -->
                        <div
                            x-show="userMenuOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute right-0 mt-2 w-64 rounded-2xl bg-white shadow-xl border border-neutral-100 p-2 z-50 text-sm"
                            role="menu"
                        >
                            <!-- User Card Header -->
                            <div class="px-3 py-2.5 bg-neutral-50 rounded-xl mb-1 border border-neutral-100">
                                <p class="font-bold text-neutral-900 text-xs truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-neutral-500 truncate" title="{{ auth()->user()->email }}">{{ auth()->user()->email }}</p>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    @if (auth()->user()->isAdmin())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700">Administrator</span>
                                    @elseif (auth()->user()->isInstructor())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700">Instructor</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">Student</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Menu Items -->
                            @if (auth()->user()->isAdmin())
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                    role="menuitem"
                                >
                                    <i class="fas fa-chart-line w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                    <span>Admin Dashboard</span>
                                </a>

                                <a
                                    href="{{ route('management.courses.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                    role="menuitem"
                                >
                                    <i class="fas fa-layer-group w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                    <span>Course Management</span>
                                </a>

                                @php
                                    $pendingApprovalsCount = \App\Models\User::where('is_approved', false)->whereNotNull('email_verified_at')->count();
                                @endphp
                                <a
                                    href="{{ route('management.users.approvals') }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                    role="menuitem"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <i class="fas fa-user-check w-4 text-center text-purple-600 text-xs" aria-hidden="true"></i>
                                        <span>User Approvals</span>
                                    </div>
                                    @if ($pendingApprovalsCount > 0)
                                        <span class="px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-[9px] font-bold">
                                            {{ $pendingApprovalsCount }}
                                        </span>
                                    @endif
                                </a>
                            @elseif (auth()->user()->isInstructor())
                                <a
                                    href="{{ route('management.courses.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                    role="menuitem"
                                >
                                    <i class="fas fa-chalkboard-teacher w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                    <span>Course Management</span>
                                </a>
                            @else
                                <a
                                    href="{{ route('student.dashboard') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                    role="menuitem"
                                >
                                    <i class="fas fa-graduation-cap w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                    <span>My Learning Dashboard</span>
                                </a>
                            @endif

                            <a
                                href="{{ route('profile.show', auth()->user()) }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                role="menuitem"
                            >
                                <i class="fas fa-user-circle w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                <span>My Profile</span>
                            </a>

                            <a
                                href="{{ route('notifications.index') }}"
                                class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-neutral-700 hover:text-lb-orange hover:bg-orange-50 transition"
                                role="menuitem"
                            >
                                <div class="flex items-center gap-2.5">
                                    <i class="fas fa-bell w-4 text-center text-lb-orange text-xs" aria-hidden="true"></i>
                                    <span>Notifications</span>
                                </div>
                                @if ($unreadHeaderCount > 0)
                                    <span class="px-1.5 py-0.5 rounded-full bg-lb-orange text-white text-[9px] font-bold">
                                        {{ $unreadHeaderCount }}
                                    </span>
                                @endif
                            </a>

                            <div class="my-1 border-t border-neutral-100"></div>

                            <!-- Sign Out in Dropdown -->
                            <form method="POST" action="{{ route('logout') }}" class="w-full" id="header-dropdown-logout-form">
                                @csrf
                                <button
                                    type="button"
                                    onclick="confirmDialog({
                                        title: 'Sign Out of LearnBuddy?',
                                        message: 'Are you sure you want to sign out of your account? You can sign back in at any time to resume your courses.',
                                        action: '{{ route('logout') }}',
                                        method: 'POST',
                                        confirmText: 'Sign Out',
                                        confirmVariant: 'danger',
                                        icon: 'fas fa-sign-out-alt'
                                    })"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer"
                                    role="menuitem"
                                >
                                    <i class="fas fa-sign-out-alt w-4 text-center text-xs" aria-hidden="true"></i>
                                    <span>Sign out</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Direct Quick Sign Out Icon -->
                    <div class="relative group shrink-0">
                        <form method="POST" action="{{ route('logout') }}" class="inline" id="header-quick-logout-form">
                            @csrf
                            <button
                                type="button"
                                onclick="confirmDialog({
                                    title: 'Sign Out of LearnBuddy?',
                                    message: 'Are you sure you want to sign out of your account? You can sign back in at any time to resume your courses.',
                                    action: '{{ route('logout') }}',
                                    method: 'POST',
                                    confirmText: 'Sign Out',
                                    confirmVariant: 'danger',
                                    icon: 'fas fa-sign-out-alt'
                                })"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-neutral-100 hover:bg-rose-50 text-neutral-500 hover:text-rose-600 transition cursor-pointer text-xs shrink-0"
                                aria-label="Sign out"
                            >
                                <i class="fas fa-sign-out-alt text-sm" aria-hidden="true"></i>
                            </button>
                        </form>
                        <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-full bg-neutral-900 text-white text-[11px] font-semibold whitespace-nowrap shadow-lg opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-150 z-50">
                            Sign out
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-neutral-900 rotate-45"></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="hidden sm:flex items-center gap-2 shrink-0">
                    <x-button
                        as="a"
                        href="{{ route('login') }}"
                        variant="dark"
                        size="sm"
                        class="whitespace-nowrap"
                    >
                        Log in
                    </x-button>

                    <x-button
                        as="a"
                        href="{{ route('register') }}"
                        variant="primary"
                        size="sm"
                        class="whitespace-nowrap"
                    >
                        Register
                    </x-button>
                </div>
            @endauth

            <!-- Mobile Hamburger Toggle Button -->
            <button
                type="button"
                @click="mobileNavOpen = !mobileNavOpen"
                class="lg:hidden w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center text-neutral-800 hover:text-lb-orange hover:bg-neutral-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer shrink-0"
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
                            <img src="{{ asset('images/logo.png') }}" alt="LearnBuddy" class="h-8 sm:h-9 w-auto object-contain">
                        @else
                            <span class="text-xl sm:text-2xl font-extrabold text-lb-black">Learn<span class="text-lb-orange">Buddy</span>.</span>
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
                        href="{{ route('courses.index') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium transition {{ request()->routeIs('courses.*') ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-800 hover:bg-neutral-50' }}"
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
                        href="{{ route('become-instructor') }}"
                        @click="mobileNavOpen = false"
                        class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-semibold transition {{ request()->routeIs('become-instructor*') ? 'bg-orange-50 text-lb-orange' : 'text-neutral-800 hover:bg-neutral-50' }}"
                    >
                        <span class="flex items-center gap-2">
                            <i class="fas fa-chalkboard-teacher text-xs text-lb-orange" aria-hidden="true"></i>
                            <span>Teach on LearnBuddy</span>
                        </span>
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

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a
                                href="{{ route('admin.dashboard') }}"
                                @click="mobileNavOpen = false"
                                class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium transition {{ request()->routeIs('admin.*') || request()->is('admin*') ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-800 hover:bg-neutral-50' }}"
                            >
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-chart-line text-lb-orange" aria-hidden="true"></i>
                                    <span>Admin Dashboard</span>
                                </span>
                                <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                            </a>
                        @endif

                        @if (auth()->user()->isAdmin() || auth()->user()->isInstructor())
                            <a
                                href="{{ route('management.courses.index') }}"
                                @click="mobileNavOpen = false"
                                class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium transition {{ request()->is('management/courses*') ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-800 hover:bg-neutral-50' }}"
                            >
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-layer-group text-lb-orange" aria-hidden="true"></i>
                                    <span>Course Management</span>
                                </span>
                                <i class="fas fa-chevron-right text-xs opacity-50" aria-hidden="true"></i>
                            </a>
                        @endif

                        @if (auth()->user()->isAdmin())
                            <a
                                href="{{ route('management.users.approvals') }}"
                                @click="mobileNavOpen = false"
                                class="flex items-center justify-between min-h-[44px] px-3 py-2.5 rounded-xl text-base font-medium transition {{ request()->routeIs('management.users.*') ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-800 hover:bg-neutral-50' }}"
                            >
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-user-check text-purple-600" aria-hidden="true"></i>
                                    <span>User Approvals</span>
                                </span>
                                @php
                                    $pendingApprovalsCount = \App\Models\User::where('is_approved', false)->whereNotNull('email_verified_at')->count();
                                @endphp
                                @if ($pendingApprovalsCount > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-xs font-bold">
                                        {{ $pendingApprovalsCount }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    @endauth
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

                    @if (auth()->user()->isAdmin())
                        <x-button
                            as="a"
                            href="{{ route('admin.dashboard') }}"
                            variant="primary"
                            class="w-full text-center"
                            @click="mobileNavOpen = false"
                        >
                            Admin Dashboard
                        </x-button>
                    @elseif (auth()->user()->isInstructor())
                        <x-button
                            as="a"
                            href="{{ route('management.courses.index') }}"
                            variant="primary"
                            class="w-full text-center"
                            @click="mobileNavOpen = false"
                        >
                            Course Management
                        </x-button>
                    @else
                        <x-button
                            as="a"
                            href="{{ route('student.dashboard') }}"
                            variant="primary"
                            class="w-full text-center"
                            @click="mobileNavOpen = false"
                        >
                            My Learning Dashboard
                        </x-button>
                    @endif

                    <x-button
                        as="a"
                        href="{{ route('profile.show', auth()->user()) }}"
                        variant="dark"
                        class="w-full text-center"
                        @click="mobileNavOpen = false"
                    >
                        My Profile
                    </x-button>

                    <form method="POST" action="{{ route('logout') }}" class="w-full" id="header-mobile-logout-form">
                        @csrf
                        <button
                            type="button"
                            onclick="confirmDialog({
                                title: 'Sign Out of LearnBuddy?',
                                message: 'Are you sure you want to sign out of your account? You can sign back in at any time to resume your courses.',
                                action: '{{ route('logout') }}',
                                method: 'POST',
                                confirmText: 'Sign Out',
                                confirmVariant: 'danger',
                                icon: 'fas fa-sign-out-alt'
                            })"
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
