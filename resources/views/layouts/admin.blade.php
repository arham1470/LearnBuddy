<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - LearnBuddy Super Admin</title>

    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js for smooth data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --orange: #FF5F00;
            --orange-light: #FF8A3D;
            --orange-dark: #D94E00;
            --white: #FFFFFF;
            --off-white: #F7F7F5;
            --gray-light: #E5E5E5;
            --gray-mid: #6B6B6B;
            --black: #0A0A0A;
            --near-black: #1A1A1A;
            --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--off-white);
            color: var(--black);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Ambient gradient glow in the top right (matching reference UI) */
        .lb-canvas-glow {
            position: absolute;
            top: 0;
            right: 0;
            width: 700px;
            height: 480px;
            background: radial-gradient(circle at 85% 15%, rgba(255, 95, 0, 0.12) 0%, rgba(255, 138, 61, 0.05) 45%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Sidebar bottom wave glow */
        .lb-sidebar-glow {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 180px;
            background: radial-gradient(circle at 30% 90%, rgba(255, 95, 0, 0.18) 0%, transparent 65%);
            pointer-events: none;
        }

        /* Card lifting hover */
        .lb-card {
            background-color: #FFFFFF;
            border: 1px solid #E5E5E5;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .lb-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }

        /* Featured KPI Card */
        .lb-card-featured {
            background: linear-gradient(135deg, #FF5F00 0%, #FF8A3D 100%);
            border: 1px solid rgba(255, 95, 0, 0.3);
            box-shadow: 0 6px 24px rgba(255, 95, 0, 0.35);
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
        }

        .lb-card-featured:hover {
            box-shadow: 0 10px 30px rgba(255, 95, 0, 0.45);
            transform: translateY(-2px);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #FF5F00;
        }

        /* Sidebar: strictly fixed and non-scrollable */
        #admin-sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
            overflow: hidden !important;
            overscroll-behavior: none !important;
            z-index: 40 !important;
        }

        aside,
        aside * {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        aside::-webkit-scrollbar,
        aside *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>
</head>

<body class="h-full bg-[#F7F7F5] text-[#0A0A0A] antialiased" x-data="{
    sidebarOpen: true,
    mobileOpen: false,
    searchModal: false,
    userDropdown: false,
    dateDropdown: false,
    activeGroup: '{{ request()->routeIs('admin.courses.*', 'admin.categories.*') ? 'academic' : (request()->routeIs('admin.batches.*', 'admin.locations.*') ? 'campus' : (request()->routeIs('admin.enrollments.*') ? 'students' : (request()->routeIs('admin.assignments.*', 'admin.quizzes.*') ? 'grading' : (request()->routeIs('admin.orders.*') ? 'finance' : (request()->routeIs('admin.users.*', 'admin.approvals.*') ? 'security' : (request()->routeIs('admin.settings.*') ? 'system' : '')))))) }}',
    toggleGroup(name) {
        this.activeGroup = this.activeGroup === name ? null : name;
    }
}" @keydown.window.ctrl.k.prevent="searchModal = true" @keydown.window.cmd.k.prevent="searchModal = true" @keydown.window.escape="searchModal = false; userDropdown = false; dateDropdown = false; mobileOpen = false;">

    <!-- Mobile Drawer Overlay -->
    <div x-show="mobileOpen" x-cloak x-transition.opacity.300ms class="fixed inset-0 z-40 bg-black/60 lg:hidden" @click="mobileOpen = false"></div>

    <div class="min-h-screen flex flex-row w-full relative">

        <!-- ============================================================
             LEFT SIDEBAR (Fixed & Non-scrollable #1A1A1A Theme)
             ============================================================ -->
        <aside id="admin-sidebar" :class="{
            'w-[280px]': sidebarOpen,
            'w-[80px]': !sidebarOpen,
            'translate-x-0': mobileOpen,
            '-translate-x-full lg:translate-x-0': !mobileOpen
        }" class="fixed inset-y-0 left-0 z-40 bg-[#1A1A1A] border-r border-white/5 flex flex-col justify-between transition-all duration-300 ease-in-out select-none overflow-hidden h-screen">

            <!-- Sidebar Ambient Glow at Bottom -->
            <div class="lb-sidebar-glow"></div>

            <!-- Top Brand & Navigation -->
            <div class="flex flex-col flex-1 min-h-0 overflow-hidden">

                <!-- Logo & Brand Header -->
                <div :class="sidebarOpen ? 'px-4 justify-center relative' : 'px-0 justify-center'" class="h-[68px] min-h-[68px] flex items-center border-b border-white/5 shrink-0 transition-all">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center group">
                        <!-- Full Dark Mode Logo when expanded (Centered & Scaled Up) -->
                        <img x-show="sidebarOpen" x-cloak
                             src="{{ asset('images/logo-dark.png') }}"
                             alt="LearnBuddy"
                             class="h-11 w-auto max-w-[210px] object-contain group-hover:opacity-90 group-hover:scale-[1.02] transition-all">

                        <!-- Compact Icon Mark when collapsed -->
                        <div x-show="!sidebarOpen" x-cloak class="w-10 h-10 overflow-hidden flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo-dark.png') }}"
                                 alt="LearnBuddy"
                                 class="h-10 max-w-none object-left object-cover group-hover:scale-105 transition-transform"
                                 style="width: auto; height: 40px; object-position: left center;">
                        </div>
                    </a>

                    <!-- Close button on mobile -->
                    <button @click="mobileOpen = false" x-show="sidebarOpen" class="absolute right-4 lg:hidden text-white/60 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Navigation Links List (Fixed, Non-scrollable) -->
                <nav class="flex-1 px-3 py-3 space-y-1 overflow-hidden select-none">

                    <!-- 1. Dashboard (Active Solid Orange Pill) -->
                    <a href="{{ route('admin.dashboard') }}"
                       :class="sidebarOpen ? 'justify-start px-3.5' : 'justify-center px-0'"
                       class="flex items-center gap-3.5 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF5F00] text-white shadow-md shadow-orange-500/25 font-semibold' : 'text-white/70 hover:text-white hover:bg-white/5 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-white/60 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] whitespace-nowrap">Dashboard</span>
                    </a>

                    <!-- 2. Academic & Courses -->
                    <div class="pt-1">
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('academic')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Academic &amp; Courses</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'academic'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'academic'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.courses.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.courses.index') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">All Courses</a>
                            <a href="{{ route('admin.categories.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">Course Categories</a>
                            <a href="{{ route('admin.courses.create') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.courses.create') ? 'text-[#FF5F00] bg-orange-500/10 font-bold' : 'text-[#FF5F00] hover:text-[#FF8A3D] font-medium' }}">+ Add New Course</a>
                        </div>
                    </div>

                    <!-- 3. Campus & Batches -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('campus')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Campus &amp; Batches</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'campus'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'campus'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.batches.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.batches.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">Batches &amp; Schedules</a>
                            <a href="{{ route('admin.locations.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.locations.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">Physical Campuses</a>
                        </div>
                    </div>

                    <!-- 4. Student Learning & Progress -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('students')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Student Learning &amp; Progress</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'students'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'students'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.enrollments.index') }}" class="block px-3 py-1.5 text-[0.8125rem] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition-colors">All Enrollments</a>
                        </div>
                    </div>

                    <!-- 5. Assessments & Grading -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('grading')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Assessments &amp; Grading</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'grading'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'grading'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.assignments.index') }}" class="block px-3 py-1.5 text-[0.8125rem] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Course Assignments</a>
                            <a href="{{ route('admin.quizzes.index') }}" class="block px-3 py-1.5 text-[0.8125rem] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Course Quizzes</a>
                        </div>
                    </div>

                    <!-- 6. Financial & Orders -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('finance')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Financial &amp; Orders</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'finance'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'finance'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.orders.index') }}" class="block px-3 py-1.5 text-[0.8125rem] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Orders &amp; Payments</a>
                        </div>
                    </div>

                    <!-- 7. User & Security -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('security')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">User &amp; Security</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'security'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'security'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.users.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.users.index') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">Users Directory</a>
                            <a href="{{ route('admin.approvals.index') }}" class="block px-3 py-1.5 text-[0.8125rem] rounded-lg transition-colors {{ request()->routeIs('admin.approvals.*') ? 'text-white bg-white/10 font-semibold' : 'text-white/60 hover:text-white hover:bg-white/5' }}">Pending Approvals</a>
                        </div>
                    </div>

                    <!-- 8. Analytics & System -->
                    <div>
                        <button @click="if (!sidebarOpen) { sidebarOpen = true }; toggleGroup('system')" type="button"
                                :class="sidebarOpen ? 'justify-between px-3.5' : 'justify-center px-0'"
                                class="w-full flex items-center py-2.5 rounded-xl text-white/75 hover:text-white hover:bg-white/5 transition-all text-left group">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 text-white/60 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="text-[0.875rem] font-medium whitespace-nowrap">Analytics &amp; System</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="{'rotate-90 text-white': activeGroup === 'system'}" class="w-4 h-4 text-white/40 group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="sidebarOpen && activeGroup === 'system'" x-cloak class="pl-11 pr-2 py-1 space-y-1">
                            <a href="{{ route('admin.settings.index') }}" class="block px-3 py-1.5 text-[0.8125rem] text-white/60 hover:text-white hover:bg-white/5 rounded-lg transition-colors">Platform Settings</a>
                        </div>
                    </div>

                </nav>

                <!-- Bottom "Create & Manage Your Courses" Card (exact visual match) -->
                <div x-show="sidebarOpen" x-cloak class="p-3 z-10 shrink-0">
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-[#242424] to-[#1E1E1E] border border-[#FF5F00]/25 shadow-xl shadow-black/40 text-center relative overflow-hidden group">
                        <!-- Top subtle glow strip -->
                        <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#FF5F00] to-transparent"></div>

                        <!-- 3D Graduation Cap Icon Box -->
                        <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-orange-500/15 border border-orange-500/30 flex items-center justify-center text-[#FF5F00] shadow-md shadow-orange-500/10 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.24.09-.367.09a.747.747 0 0 1-.367-.09l-.003-.001a49.95 49.95 0 0 0-9.902-3.913.75.75 0 0 1-.23-1.337A60.65 60.65 0 0 1 11.7 2.805Z" />
                                <path d="M13.06 15.473a48.455 48.455 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.456.711l-7.25 3.107a.75.75 0 0 1-.59 0l-7.25-3.107a.75.75 0 0 1-.456-.711c.035-1.441.121-2.87.255-4.284a48.455 48.455 0 0 1 7.666 3.282.75.75 0 0 0 .59 0l.085-.036.085.036Z" />
                            </svg>
                        </div>

                        <h4 class="text-white font-bold text-[0.8125rem] leading-tight mb-1">Create &amp; Manage<br>Your Courses</h4>
                        <p class="text-white/50 text-[0.6875rem] leading-snug mb-2.5">Build, schedule and track learning in one place.</p>

                        <a href="{{ route('admin.courses.create') }}"
                           class="w-fit mx-auto inline-flex items-center justify-center gap-1.5 py-1.5 px-3.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white font-bold text-[0.78rem] shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>New Course</span>
                        </a>
                    </div>
                </div>

            </div>

        </aside>

        <!-- ============================================================
             MAIN CONTENT WRAPPER (Offset by fixed sidebar width on desktop)
             ============================================================ -->
        <div :class="{
            'lg:pl-[280px]': sidebarOpen,
            'lg:pl-[80px]': !sidebarOpen
        }" class="flex-1 flex flex-col min-w-0 bg-[#F7F7F5] relative min-h-screen transition-all duration-300 ease-in-out">

            <!-- Ambient Warm Glow at Top-Right Canvas -->
            <div class="lb-canvas-glow"></div>

            <!-- ============================================================
                 TOPBAR (Clean White Navigation Bar with Quick Actions)
                 ============================================================ -->
            <header class="h-[68px] min-h-[68px] bg-white border-b border-[#E5E5E5] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">

                <!-- Left: Sidebar Toggle & Global Search Bar -->
                <div class="flex items-center gap-3 sm:gap-4 flex-1 max-w-xl">
                    <!-- Hamburger / Sidebar Toggle -->
                    <button @click="if (window.innerWidth < 1024) { mobileOpen = !mobileOpen } else { sidebarOpen = !sidebarOpen }"
                            class="w-10 h-10 rounded-xl bg-[#F7F7F5] hover:bg-[#FFF0E6] border border-[#E5E5E5] hover:border-orange-300 flex items-center justify-center text-[#6B6B6B] hover:text-[#FF5F00] transition-all cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Search Input Box with Ctrl+K shortcut -->
                    <div @click="searchModal = true"
                         class="w-full flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] hover:border-orange-300 transition-colors cursor-pointer group">
                        <svg class="w-4 h-4 text-[#6B6B6B] group-hover:text-[#FF5F00] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-[0.84rem] text-[#6B6B6B] flex-1 truncate">Search students, courses, batches...</span>
                        <kbd class="hidden sm:inline-flex items-center px-2 py-0.5 text-[0.6875rem] font-semibold text-[#6B6B6B] bg-white border border-[#E5E5E5] rounded-md shadow-xs shrink-0">Ctrl K</kbd>
                    </div>
                </div>

                <!-- Right: Notification, Storefront Link, Role Pill & User Avatar -->
                <div class="flex items-center gap-2.5 sm:gap-4 ml-4 shrink-0">

                    <!-- Notification Bell with Active Indicator -->
                    <a href="{{ route('notifications.index') }}" class="w-10 h-10 rounded-xl bg-[#F7F7F5] hover:bg-[#FFF0E6] border border-[#E5E5E5] hover:border-orange-300 flex items-center justify-center text-[#6B6B6B] hover:text-[#FF5F00] transition-all relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <!-- Orange Notification Dot -->
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#FF5F00] ring-2 ring-white"></span>
                    </a>

                    <!-- View Storefront Button -->
                    <a href="{{ route('home') }}" target="_blank"
                       class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-[#F7F7F5] border border-[#E5E5E5] text-[0.8125rem] font-semibold text-[#0A0A0A] hover:text-[#FF5F00] transition-colors shadow-xs">
                        <span>View Storefront</span>
                        <svg class="w-3.5 h-3.5 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    <!-- Role Badge: Super Admin -->
                    <div class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#FFF0E6] border border-[#FF5F00]/25 text-[#FF5F00] font-bold text-[0.78rem]">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                        </svg>
                        <span>Super Admin</span>
                    </div>

                    <!-- User Avatar & Dropdown -->
                    <div class="relative" @click.outside="userDropdown = false">
                        <button @click="userDropdown = !userDropdown"
                                class="w-10 h-10 rounded-full bg-[#1A1A1A] hover:ring-2 hover:ring-[#FF5F00] text-white font-bold text-[0.875rem] flex items-center justify-center transition-all shadow-md cursor-pointer select-none">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'SA' }}
                        </button>

                        <!-- User Profile Dropdown Menu -->
                        <div x-show="userDropdown" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#E5E5E5] py-2 z-50">
                            <div class="px-4 py-2 border-b border-[#E5E5E5]">
                                <div class="text-[0.875rem] font-bold text-[#0A0A0A] truncate">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                                <div class="text-[0.72rem] text-[#6B6B6B] truncate">{{ auth()->user()->email ?? 'admin@learnbuddy.lk' }}</div>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2 text-[0.8125rem] text-[#0A0A0A] hover:bg-[#FFF0E6] hover:text-[#FF5F00] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>Storefront Homepage</span>
                                </a>
                                <a href="{{ route('settings.notifications.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-[0.8125rem] text-[#0A0A0A] hover:bg-[#FFF0E6] hover:text-[#FF5F00] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Notification Settings</span>
                                </a>
                            </div>
                            <div class="border-t border-[#E5E5E5] pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-[0.8125rem] text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </header>

            <!-- ============================================================
                 MAIN VIEW CONTENT CONTAINER
                 ============================================================ -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-[1440px] w-full mx-auto relative z-10">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- ============================================================
         GLOBAL SEARCH MODAL (Triggered by Ctrl+K)
         ============================================================ -->
    <div x-show="searchModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" @click="searchModal = false"></div>

        <div class="mx-auto max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all border border-[#E5E5E5] relative">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-[#6B6B6B]" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
                <input type="text"
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-[#0A0A0A] placeholder:text-[#6B6B6B] focus:ring-0 sm:text-sm outline-none"
                       placeholder="Quick jump to courses, students, batches, or settings..."
                       x-ref="searchInput"
                       @keydown.escape="searchModal = false">
            </div>

            <!-- Quick Jump Shortcuts -->
            <div class="border-t border-[#E5E5E5] px-4 py-3 bg-[#F7F7F5]">
                <div class="text-[0.72rem] font-bold uppercase tracking-wider text-[#6B6B6B] mb-2">Platform Quick Jumps</div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>All Courses</span>
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span>New Course</span>
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.24.09-.367.09a.747.747 0 0 1-.367-.09l-.003-.001a49.95 49.95 0 0 0-9.902-3.913.75.75 0 0 1-.23-1.337A60.65 60.65 0 0 1 11.7 2.805Z"/><path d="M13.06 15.473a48.455 48.455 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.456.711l-7.25 3.107a.75.75 0 0 1-.59 0l-7.25-3.107a.75.75 0 0 1-.456-.711c.035-1.441.121-2.87.255-4.284a48.455 48.455 0 0 1 7.666 3.282.75.75 0 0 0 .59 0l.085-.036.085.036Z"/></svg>
                        <span>Enrollments</span>
                    </a>
                    <a href="{{ route('admin.batches.index') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Batches &amp; Schedules</span>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white hover:text-[#FF5F00] transition-colors text-[#0A0A0A]">
                        <svg class="w-4 h-4 text-[#FF5F00] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Orders &amp; Revenue</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
