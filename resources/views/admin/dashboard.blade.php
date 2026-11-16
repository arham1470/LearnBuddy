@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- ============================================================
         1. DASHBOARD HEADER
         ============================================================ -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="text-[0.9375rem] font-medium text-[#6B6B6B] flex items-center gap-1.5">
                <span>Good Morning, Super Admin</span>
                <span class="inline-block animate-wave origin-bottom-right">👋</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#0A0A0A] tracking-tight mt-1">Dashboard</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-0.5">Here's what's happening with your learning platform today.</p>
        </div>

        <!-- Date Selector Dropdown -->
        <div class="relative shrink-0" @click.outside="dateDropdown = false">
            <button @click="dateDropdown = !dateDropdown"
                    type="button"
                    class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-[#E5E5E5] hover:border-orange-300 text-[0.84rem] font-semibold text-[#0A0A0A] shadow-xs hover:shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4 text-[#FF5F00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Nov 16, 2026</span>
                <svg class="w-3.5 h-3.5 text-[#6B6B6B] transition-transform" :class="{'rotate-180': dateDropdown}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown options -->
            <div x-show="dateDropdown" x-cloak x-transition
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-[#E5E5E5] py-1.5 z-40 text-sm">
                <button type="button" @click="dateDropdown = false" class="w-full text-left px-4 py-2 text-[#FF5F00] font-bold bg-[#FFF0E6]">Today (Nov 16, 2026)</button>
                <button type="button" @click="dateDropdown = false" class="w-full text-left px-4 py-2 text-[#0A0A0A] hover:bg-[#F7F7F5]">Yesterday</button>
                <button type="button" @click="dateDropdown = false" class="w-full text-left px-4 py-2 text-[#0A0A0A] hover:bg-[#F7F7F5]">Last 7 Days</button>
                <button type="button" @click="dateDropdown = false" class="w-full text-left px-4 py-2 text-[#0A0A0A] hover:bg-[#F7F7F5]">This Month</button>
            </div>
        </div>
    </div>

    <!-- ============================================================
         2. STATISTICS CARDS (7 Modern Metric Cards)
         ============================================================ -->
    <div class="space-y-4">

        <!-- Row 1: 4 Primary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            <!-- Card 1: Active Courses (FEATURED ORANGE GRADIENT CARD) -->
            <div class="lb-card-featured rounded-2xl p-5 sm:p-6 flex flex-col justify-between min-h-[148px] relative overflow-hidden group">
                <!-- Decorative Watermark Open Book Illustration -->
                <div class="absolute -right-3 -bottom-3 text-white/10 group-hover:text-white/15 transition-all pointer-events-none">
                    <svg class="w-28 h-28" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>

                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-xs flex items-center justify-center text-white shrink-0 shadow-xs">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.24.09-.367.09a.747.747 0 0 1-.367-.09l-.003-.001a49.95 49.95 0 0 0-9.902-3.913.75.75 0 0 1-.23-1.337A60.65 60.65 0 0 1 11.7 2.805Z" />
                            <path d="M13.06 15.473a48.455 48.455 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.456.711l-7.25 3.107a.75.75 0 0 1-.59 0l-7.25-3.107a.75.75 0 0 1-.456-.711c.035-1.441.121-2.87.255-4.284a48.455 48.455 0 0 1 7.666 3.282.75.75 0 0 0 .59 0l.085-.036.085.036Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-white/90">Active Courses</div>
                        <div class="text-3xl font-extrabold text-white tracking-tight leading-tight">{{ $activeCourses }}</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-white/90 font-medium relative z-10">
                    <span>Published courses</span>
                    <svg class="w-4 h-4 text-emerald-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Total Learners -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Total Learners</div>
                        <div class="text-3xl font-extrabold text-[#0A0A0A] tracking-tight leading-tight">{{ $totalLearners }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-[#6B6B6B]">
                    <span>Enrolled Sri Lankan students</span>
                    <span class="text-xs">👥</span>
                </div>
            </div>

            <!-- Card 3: Enrollments -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Enrollments</div>
                        <div class="text-3xl font-extrabold text-[#0A0A0A] tracking-tight leading-tight">{{ $totalEnrollments }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-[#6B6B6B]">
                    <span>Total course seats</span>
                    <span class="text-xs">🪑</span>
                </div>
            </div>

            <!-- Card 4: Total Revenue -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Total Revenue</div>
                        <div class="text-2xl sm:text-[1.65rem] font-extrabold text-[#0A0A0A] tracking-tight leading-tight">{{ $totalRevenue }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-[#6B6B6B]">
                    <span>All settled payments</span>
                    <span class="text-xs">💳</span>
                </div>
            </div>

        </div>

        <!-- Row 2: Secondary KPIs & Enrollment Progress -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

            <!-- Card 5: Campus Attendance -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Campus Attendance</div>
                        <div class="text-3xl font-extrabold text-[#0A0A0A] tracking-tight leading-tight">{{ $campusAttendance }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-[#6B6B6B]">
                    <span>Physical check-in rate</span>
                    <svg class="w-3.5 h-3.5 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </div>
            </div>

            <!-- Card 6: Pending Grading -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[0.8125rem] font-semibold text-[#6B6B6B]">Pending Grading</div>
                        <div class="text-3xl font-extrabold text-[#0A0A0A] tracking-tight leading-tight">{{ $pendingGrading }}</div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-1.5 text-[0.78rem] text-[#6B6B6B]">
                    <span>Submissions needing review</span>
                    <svg class="w-3.5 h-3.5 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </div>
            </div>

            <!-- Card 7: Enrollment Progress (Spanning 2 columns on lg) -->
            <div class="lb-card p-5 sm:p-6 flex flex-col justify-between min-h-[148px] md:col-span-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] flex items-center justify-center text-[#FF5F00] shrink-0 border border-orange-200/50">
                            <!-- Target / Bullseye Icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                <circle cx="12" cy="12" r="6" stroke-width="2"/>
                                <circle cx="12" cy="12" r="2" stroke-width="2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[0.9375rem] font-bold text-[#0A0A0A]">Enrollment Progress</div>
                            <div class="text-[0.8125rem] text-[#6B6B6B] mt-0.5">{{ $filledSeats }} of {{ $totalCapacity }} seats filled</div>
                        </div>
                    </div>
                    <!-- Completion pill badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.8125rem] font-bold bg-[#FFF0E6] text-[#FF5F00] border border-orange-200/60">
                        {{ $percentFilled }}%
                    </span>
                </div>

                <!-- Smooth Orange Progress Bar -->
                <div class="mt-4">
                    <div class="w-full h-3 rounded-full bg-[#E5E5E5] overflow-hidden">
                        <div class="h-full rounded-full bg-[#FF5F00] transition-all duration-700 ease-out" style="width: {{ $percentFilled }}%;"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ============================================================
         3. MIDDLE SECTION: RECENT ENROLLMENTS & TREND CHART
         ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Recent Course Enrollments (Width ~62% -> 7 or 8 cols) -->
        <div class="lg:col-span-7 xl:col-span-7 lb-card overflow-hidden">
            <!-- Card Header -->
            <div class="p-5 sm:px-6 sm:py-5 border-b border-[#E5E5E5] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="text-[#FF5F00]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-[1.0625rem] font-bold text-[#0A0A0A]">Recent Course Enrollments</h3>
                </div>
                <a href="{{ route('admin.enrollments.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#E5E5E5] hover:border-orange-300 bg-[#F7F7F5] hover:bg-[#FFF0E6] text-[0.78rem] font-bold text-[#6B6B6B] hover:text-[#FF5F00] transition-colors">
                    <span>View All</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                            <th scope="col" class="py-3 px-5">Student</th>
                            <th scope="col" class="py-3 px-4">Course</th>
                            <th scope="col" class="py-3 px-3">Mode</th>
                            <th scope="col" class="py-3 px-3">Status</th>
                            <th scope="col" class="py-3 px-5 text-right">Enrolled On</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @forelse($recentEnrollments as $item)
                        <tr class="hover:bg-[#FFFDFB] transition-colors">
                            <!-- Student Avatar & Name -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#FFF0E6] border border-[#FF5F00]/25 text-[#FF5F00] font-bold text-[0.72rem] flex items-center justify-center shrink-0">
                                        {{ $item['initials'] }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-[#0A0A0A] text-[0.84rem] truncate">{{ $item['student_name'] }}</div>
                                        @if(!empty($item['student_email']))
                                            <div class="text-[0.6875rem] text-[#6B6B6B] truncate">{{ $item['student_email'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Course Title (truncated with title tooltip) -->
                            <td class="py-3.5 px-4 max-w-[200px]" title="{{ $item['course_title'] }}">
                                <div class="text-[0.84rem] text-[#0A0A0A] font-medium truncate">
                                    {{ $item['course_title'] }}
                                </div>
                            </td>

                            <!-- Mode Badge -->
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.72rem] font-bold bg-[#EBF5FF] text-[#2B6CB0] border border-blue-200/60">
                                    {{ $item['delivery_mode'] }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-3">
                                @if($item['status_type'] === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.72rem] font-bold bg-[#DEF7EC] text-[#03543F] border border-emerald-200/60">
                                        {{ $item['status'] }}
                                    </span>
                                @elseif($item['status_type'] === 'warning')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.72rem] font-bold bg-[#FEF3C7] text-[#92400E] border border-amber-200/60">
                                        {{ $item['status'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.72rem] font-bold bg-[#F3F4F6] text-[#4B5563] border border-gray-200">
                                        {{ $item['status'] }}
                                    </span>
                                @endif
                            </td>

                            <!-- Enrolled Date -->
                            <td class="py-3.5 px-5 text-right text-[0.78rem] text-[#6B6B6B] font-medium">
                                {{ $item['enrolled_at'] }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[#6B6B6B]">No course enrollments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Course Enrollments Overview Chart (Width ~38% -> 5 cols) -->
        <div class="lg:col-span-5 xl:col-span-5 lb-card p-5 sm:p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="text-[#FF5F00]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-[1.0625rem] font-bold text-[#0A0A0A]">Course Enrollments Overview</h3>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="relative w-full h-[270px] pt-2">
                <canvas id="enrollmentTrendChart" class="w-full h-full"></canvas>

                <!-- Floating Tooltip Pill matching reference screenshot (Nov 16: 5 Enrollments) -->
                <div class="absolute top-10 right-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-[#FF5F00] text-white text-[0.75rem] font-bold shadow-lg shadow-orange-500/35 pointer-events-none animate-bounce-slow">
                    <span>5 Enrollments</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================
         4. BOTTOM SECTION: UPCOMING & ONGOING LIVE SESSIONS
         ============================================================ -->
    <div class="lb-card overflow-hidden">
        <!-- Section Header -->
        <div class="p-5 sm:px-6 sm:py-5 border-b border-[#E5E5E5] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="text-[#FF5F00]">
                    <!-- Video camera icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-[1.0625rem] font-bold text-[#0A0A0A]">Upcoming &amp; Ongoing Live Sessions</h3>
            </div>
        </div>

        @if($liveClasses->isNotEmpty())
            <!-- Live sessions table if available -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                            <th class="py-3 px-5">Session</th>
                            <th class="py-3 px-4">Course</th>
                            <th class="py-3 px-4">Lecturer</th>
                            <th class="py-3 px-4">Date &amp; Time</th>
                            <th class="py-3 px-5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($liveClasses as $session)
                        <tr class="hover:bg-[#FFFDFB] transition-colors">
                            <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">{{ $session->title }}</td>
                            <td class="py-3.5 px-4 text-[#6B6B6B]">{{ $session->course->title ?? 'N/A' }}</td>
                            <td class="py-3.5 px-4 text-[#0A0A0A]">{{ $session->instructor->name ?? 'Instructor' }}</td>
                            <td class="py-3.5 px-4 text-[#6B6B6B]">{{ \Carbon\Carbon::parse($session->scheduled_start_time)->format('M d, Y h:i A') }}</td>
                            <td class="py-3.5 px-5 text-right">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">{{ ucfirst($session->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Balanced Modern Empty State (matching reference image) -->
            <div class="py-14 px-6 text-center">
                <!-- Icon container -->
                <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-[#FFF0E6] border border-orange-200/50 flex items-center justify-center text-[#FF5F00] shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h4 class="text-[1.0625rem] font-bold text-[#0A0A0A]">No live classes</h4>
                <p class="text-[0.8125rem] text-[#6B6B6B] mt-1 max-w-sm mx-auto">There are no upcoming or ongoing sessions at the moment.</p>
            </div>
        @endif
    </div>

</div>

<!-- Initialize Chart.js Enrollment Line Chart -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('enrollmentTrendChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const dataValues = @json($chartValues);

        // Create gradient fill matching reference UI
        const chartCtx = ctx.getContext('2d');
        const gradient = chartCtx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(255, 95, 0, 0.28)');
        gradient.addColorStop(0.65, 'rgba(255, 95, 0, 0.08)');
        gradient.addColorStop(1, 'rgba(255, 95, 0, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Enrollments',
                    data: dataValues,
                    borderColor: '#FF5F00',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.42, // Smooth bezier curve
                    pointBackgroundColor: '#FF5F00',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2.5,
                    pointRadius: 4.5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#FF5F00',
                    pointHoverBorderColor: '#FFFFFF',
                    pointHoverBorderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1A1A1A',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Enrollments';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '500' },
                            color: '#6B6B6B'
                        }
                    },
                    y: {
                        min: 0,
                        max: 10,
                        ticks: {
                            stepSize: 2,
                            font: { family: 'Inter', size: 11, weight: '500' },
                            color: '#6B6B6B'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
