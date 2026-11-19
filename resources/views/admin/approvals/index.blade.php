@extends('layouts.admin')

@section('title', 'Pending Approvals & Applications')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
                <a href="{{ route('admin.users.index') }}" class="hover:text-[#FF5F00] transition-colors">Users &amp; Security</a>
                <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#0A0A0A] font-semibold">Approvals</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Access &amp; Instructor Approvals</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review new instructor applications, credentials, and pending student access requests.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}"
               class="px-4 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all flex items-center gap-2">
                <i class="fas fa-user-plus text-xs"></i>
                <span>Add User</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                Users Directory
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <i class="fas fa-check-circle text-emerald-500 text-lg shrink-0 mt-0.5"></i>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pending -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Awaiting Review</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending_count'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Total queued submissions</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-clock text-lg"></i>
            </div>
        </div>

        <!-- Instructor Applications -->
        <div class="lb-card p-5 flex items-center justify-between border-l-4 border-l-[#FF5F00]">
            <div>
                <p class="text-xs font-bold text-[#FF5F00] uppercase tracking-wider">Instructor Applicants</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['pending_instructors'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Applications needing vetting</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#FF5F00] flex items-center justify-center shrink-0">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
        </div>

        <!-- Student Registrations -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Pending Students</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['pending_students'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Verified OTP registrations</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-user-graduate text-lg"></i>
            </div>
        </div>

        <!-- Approved Today -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Approved Today</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['approved_today'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Activated during {{ today()->format('M d') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-check-double text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Category Tabs -->
    <div class="lb-card p-4 space-y-4">
        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 border-b border-[#E5E5E5] pb-3 text-sm">
            <a href="{{ route('admin.approvals.index', ['tab' => 'all', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl font-bold transition {{ ($tab ?? 'all') === 'all' ? 'bg-[#1A1A1A] text-white shadow-xs' : 'text-[#6B6B6B] hover:text-[#0A0A0A] hover:bg-[#F7F7F5]' }}">
                All Requests ({{ $stats['pending_count'] ?? 0 }})
            </a>
            <a href="{{ route('admin.approvals.index', ['tab' => 'instructors', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-2 {{ ($tab ?? '') === 'instructors' ? 'bg-[#FF5F00] text-white shadow-xs shadow-orange-500/20' : 'text-[#6B6B6B] hover:text-[#FF5F00] hover:bg-[#F7F7F5]' }}">
                <i class="fas fa-chalkboard-teacher text-xs"></i>
                <span>Instructors</span>
                @if(($stats['pending_instructors'] ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($tab ?? '') === 'instructors' ? 'bg-white text-[#FF5F00]' : 'bg-orange-100 text-[#FF5F00]' }}">
                        {{ $stats['pending_instructors'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.approvals.index', ['tab' => 'students', 'search' => request('search')]) }}"
               class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-2 {{ ($tab ?? '') === 'students' ? 'bg-[#1A1A1A] text-white shadow-xs' : 'text-[#6B6B6B] hover:text-[#0A0A0A] hover:bg-[#F7F7F5]' }}">
                <i class="fas fa-graduation-cap text-xs"></i>
                <span>Students</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ ($tab ?? '') === 'students' ? 'bg-white/20 text-white' : 'bg-neutral-100 text-[#6B6B6B]' }}">
                    {{ $stats['pending_students'] }}
                </span>
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.approvals.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
            <div class="relative w-full sm:max-w-md">
                <i class="fas fa-search text-xs text-[#6B6B6B] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search applicant name, email, or phone..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#0A0A0A] hover:bg-neutral-800 text-white text-sm font-bold transition shadow-xs">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.approvals.index', ['tab' => $tab ?? 'all']) }}" class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] text-[#6B6B6B] hover:text-[#0A0A0A] text-sm font-semibold transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Approvals Table -->
    <div class="lb-card overflow-hidden shadow-xs" x-data="{ expandedId: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Applicant</th>
                        <th class="py-3 px-4">Applied As</th>
                        <th class="py-3 px-4">Contact Phone</th>
                        <th class="py-3 px-4">Submitted On</th>
                        <th class="py-3 px-5 text-right">Review Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse ($users as $user)
                        @php
                            $application = $user->latestInstructorApplication;
                            $isInstructorApp = $application && $application->isPending();
                        @endphp
                        <tr class="hover:bg-[#FFFDFB] transition-colors {{ $isInstructorApp ? 'bg-orange-50/20' : '' }}">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $isInstructorApp ? 'bg-[#FF5F00] text-white shadow-xs shadow-orange-500/30' : 'bg-[#1A1A1A] text-white' }} flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#0A0A0A] text-sm leading-snug truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-[#6B6B6B] truncate">{{ $user->email }}</p>
                                    </div>
                                    @if($isInstructorApp)
                                        <button type="button"
                                                @click="expandedId = (expandedId === {{ $user->id }} ? null : {{ $user->id }})"
                                                :title="expandedId === {{ $user->id }} ? 'Close Application Dossier' : 'View Application Dossier'"
                                                :aria-label="expandedId === {{ $user->id }} ? 'Close Application Dossier' : 'View Application Dossier'"
                                                :class="expandedId === {{ $user->id }}
                                                    ? 'bg-[#0A0A0A] hover:bg-neutral-800 text-white border-[#0A0A0A] shadow-xs'
                                                    : 'bg-orange-50 hover:bg-orange-100 text-[#FF5F00] border-orange-200/90 hover:border-orange-300 shadow-2xs'"
                                                class="w-7 h-7 rounded-xl border transition-all duration-200 inline-flex items-center justify-center cursor-pointer shrink-0">
                                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200"
                                               :class="{ 'rotate-180': expandedId === {{ $user->id }} }"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-4">
                                @if($isInstructorApp)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                        <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                                        <span>Instructor Applicant</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-graduation-cap text-[10px]"></i>
                                        <span>Student</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-xs font-mono text-[#6B6B6B]">
                                {{ $user->phone ?? ($application?->phone ?? 'Not provided') }}
                            </td>

                            <td class="py-4 px-4 text-xs text-[#6B6B6B]">
                                {{ $application?->created_at?->format('M d, Y · h:i A') ?? ($user->created_at ? $user->created_at->format('M d, Y · h:i A') : '—') }}
                            </td>

                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.approvals.approve', $user) }}" class="inline-block" id="approve-form-{{ $user->id }}">
                                        @csrf
                                        <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
                                        <button type="button"
                                                data-item="{{ $user->name }} ({{ $isInstructorApp ? 'Instructor' : 'Student' }})"
                                                data-form="approve-form-{{ $user->id }}"
                                                onclick="confirmDialog({
                                                    title: 'Approve {{ $isInstructorApp ? 'Instructor Account' : 'Student Access' }}?',
                                                    itemName: '{{ $user->name }} ({{ $user->email }})',
                                                    message: '{{ $isInstructorApp ? "Approving this user will grant them Instructor permissions to create courses, schedule live classes, and manage batches." : "Approving this user will activate their account and grant platform access." }}',
                                                    targetForm: this.closest('form'),
                                                    confirmText: '{{ $isInstructorApp ? 'Approve as Instructor' : 'Approve Access' }}',
                                                    confirmVariant: 'success',
                                                    icon: 'fas fa-check-circle'
                                                })"
                                                class="px-4 py-2 rounded-xl {{ $isInstructorApp ? 'bg-[#FF5F00] hover:bg-[#e65500] text-white shadow-xs shadow-orange-500/20' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }} text-xs font-bold transition cursor-pointer">
                                            {{ $isInstructorApp ? 'Approve Instructor' : 'Approve Access' }}
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <form method="POST" action="{{ route('admin.approvals.reject', $user) }}" class="inline-block" id="reject-form-{{ $user->id }}">
                                        @csrf
                                        <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
                                        <button type="button"
                                                onclick="confirmDialog({
                                                    title: 'Reject Registration?',
                                                    itemName: '{{ $user->name }} ({{ $user->email }})',
                                                    message: 'Are you sure you want to reject this request? The account submission will be declined.',
                                                    targetForm: this.closest('form'),
                                                    confirmText: 'Reject Request',
                                                    confirmVariant: 'danger',
                                                    icon: 'fas fa-times-circle'
                                                })"
                                                class="px-3 py-2 rounded-xl border border-transparent hover:border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition cursor-pointer">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Expandable Details Row for Instructor Applications -->
                        @if($isInstructorApp)
                        <tr x-show="expandedId === {{ $user->id }}"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-[#FFFDF9]/60 border-b border-[#F0ECE1]">
                            <td colspan="5" class="p-3 sm:p-5 lg:p-6 whitespace-normal">
                                <div class="w-full max-w-5xl mx-auto bg-white rounded-2xl sm:rounded-3xl border border-orange-200/80 shadow-md shadow-orange-500/5 overflow-hidden">
                                    <!-- Top Dossier Header Banner -->
                                    <div class="bg-gradient-to-r from-orange-500/10 via-amber-500/5 to-transparent border-b border-orange-100 p-4 sm:p-6">
                                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                            <div class="space-y-1.5 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-[#FF5F00] text-white shadow-2xs">
                                                        <i class="fas fa-chalkboard-teacher text-[9px]"></i>
                                                        <span>Instructor Candidate Dossier</span>
                                                    </span>
                                                    <span class="text-[11px] text-[#6B6B6B] flex items-center gap-1">
                                                        <i class="far fa-clock text-[10px]"></i>
                                                        <span>Applied {{ $application->created_at?->diffForHumans() ?? 'recently' }}</span>
                                                    </span>
                                                </div>
                                                <h3 class="text-base sm:text-lg font-black text-[#0A0A0A] tracking-tight break-words">
                                                    {{ $application->headline ?: 'Instructor Applicant Profile' }}
                                                </h3>
                                            </div>

                                            <!-- Social & Portfolio Link Badges -->
                                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                                @if($application->linkedin_url)
                                                    <a href="{{ $application->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-all border border-blue-200/60 shadow-2xs">
                                                        <i class="fab fa-linkedin text-sm"></i>
                                                        <span>LinkedIn</span>
                                                        <i class="fas fa-external-link-alt text-[9px] opacity-60"></i>
                                                    </a>
                                                @endif
                                                @if($application->portfolio_url)
                                                    <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-800 text-xs font-bold transition-all border border-neutral-200/80 shadow-2xs">
                                                        <i class="fas fa-globe text-sm text-[#FF5F00]"></i>
                                                        <span>Portfolio / Site</span>
                                                        <i class="fas fa-external-link-alt text-[9px] opacity-60"></i>
                                                    </a>
                                                @endif
                                                @if($application->sample_video_url)
                                                    <a href="{{ $application->sample_video_url }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all border border-rose-200/70 shadow-2xs">
                                                        <i class="fas fa-play-circle text-sm text-rose-600"></i>
                                                        <span>Demo Lecture</span>
                                                        <i class="fas fa-external-link-alt text-[9px] opacity-60"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Main Content Body -->
                                    <div class="p-4 sm:p-6 space-y-6">
                                        <!-- 3-Column Info Cards Grid: Contact, Subjects, Languages -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                                            <!-- Contact Card -->
                                            <div class="p-3.5 sm:p-4 rounded-2xl bg-[#F7F7F5] border border-[#E5E5E5] flex flex-col justify-between">
                                                <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-2">
                                                    <i class="fas fa-id-card text-[#FF5F00] mr-1"></i> Applicant Contact
                                                </span>
                                                <div class="space-y-1 text-xs">
                                                    <div class="flex items-center gap-1.5 text-[#0A0A0A] font-bold truncate">
                                                        <i class="far fa-user text-[#6B6B6B] text-[11px] w-3.5"></i>
                                                        <span class="truncate">{{ $user->name }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-[#6B6B6B] truncate font-mono text-[11px]">
                                                        <i class="far fa-envelope text-[#6B6B6B] text-[11px] w-3.5"></i>
                                                        <span class="truncate">{{ $user->email }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-[#6B6B6B] font-mono text-[11px]">
                                                        <i class="fas fa-phone-alt text-[#6B6B6B] text-[10px] w-3.5"></i>
                                                        <span>{{ $user->phone ?? ($application->phone ?? 'No phone provided') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Subject Expertise -->
                                            <div class="p-3.5 sm:p-4 rounded-2xl bg-[#F7F7F5] border border-[#E5E5E5]">
                                                <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-2">
                                                    <i class="fas fa-book-open text-[#FF5F00] mr-1"></i> Subject Expertise
                                                </span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse((array)($application->subject_areas ?? []) as $subject)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-100/80 text-orange-900 border border-orange-200 text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-[#FF5F00]"></span>
                                                            <span>{{ $subject }}</span>
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-[#6B6B6B] italic">No subjects specified</span>
                                                    @endforelse
                                                </div>
                                            </div>

                                            <!-- Teaching Languages -->
                                            <div class="p-3.5 sm:p-4 rounded-2xl bg-[#F7F7F5] border border-[#E5E5E5]">
                                                <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-2">
                                                    <i class="fas fa-language text-[#FF5F00] mr-1"></i> Medium of Instruction
                                                </span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse((array)($application->languages ?? []) as $lang)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white text-neutral-800 border border-neutral-200 text-xs font-semibold shadow-2xs capitalize">
                                                            <i class="fas fa-globe text-[10px] text-neutral-400"></i>
                                                            <span>{{ $lang }}</span>
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-[#6B6B6B] italic">English</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Teaching Bio & Experience Statement -->
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-extrabold text-[#0A0A0A] uppercase tracking-wider flex items-center gap-1.5">
                                                    <i class="fas fa-quote-left text-[#FF5F00]"></i>
                                                    <span>Teaching Bio &amp; Professional Background</span>
                                                </span>
                                                <span class="text-[10px] text-[#6B6B6B]">Submitted by candidate</span>
                                            </div>
                                            <div class="p-4 sm:p-5 rounded-2xl bg-[#FDFCFB] border border-[#EFECE6] text-xs sm:text-sm text-[#262626] leading-relaxed shadow-2xs whitespace-pre-line">
                                                {{ $application->bio }}
                                            </div>
                                        </div>

                                        <!-- Sample Lecture Video Box (if available) -->
                                        @if($application->sample_video_url)
                                            <div class="p-4 rounded-2xl bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-600/30">
                                                        <i class="fas fa-play text-xs"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-extrabold text-rose-950">Teaching Sample / Demo Lecture Available</p>
                                                        <p class="text-[11px] text-rose-700/90 truncate font-mono mt-0.5">{{ $application->sample_video_url }}</p>
                                                    </div>
                                                </div>
                                                <a href="{{ $application->sample_video_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold transition shrink-0 shadow-2xs">
                                                    <span>Watch Recording</span>
                                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Review Decision Action Footer Bar -->
                                    <div class="bg-[#FAF8F5] border-t border-[#F0ECE1] p-4 sm:p-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                        <div class="text-xs text-[#6B6B6B] flex items-center gap-2">
                                            <i class="fas fa-shield-alt text-[#FF5F00]"></i>
                                            <span>Approving grants full instructor dashboard access to publish courses and schedule classes.</span>
                                        </div>
                                        <div class="flex items-center gap-2 justify-end shrink-0">
                                            <button type="button"
                                                    onclick="confirmDialog({
                                                        title: 'Reject Registration?',
                                                        itemName: '{{ $user->name }} ({{ $user->email }})',
                                                        message: 'Are you sure you want to reject this request? The account submission will be declined.',
                                                        targetForm: document.getElementById('reject-form-{{ $user->id }}'),
                                                        confirmText: 'Reject Request',
                                                        confirmVariant: 'danger',
                                                        icon: 'fas fa-times-circle'
                                                    })"
                                                    class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl border border-rose-200 hover:border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <i class="fas fa-times text-xs"></i>
                                                <span>Decline Request</span>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmDialog({
                                                        title: 'Approve Instructor Account?',
                                                        itemName: '{{ $user->name }} ({{ $user->email }})',
                                                        message: 'Approving this user will grant them Instructor permissions to create courses, schedule live classes, and manage batches.',
                                                        targetForm: document.getElementById('approve-form-{{ $user->id }}'),
                                                        confirmText: 'Approve as Instructor',
                                                        confirmVariant: 'success',
                                                        icon: 'fas fa-check-circle'
                                                    })"
                                                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-xs font-bold transition shadow-xs shadow-orange-500/25 flex items-center justify-center gap-1.5 cursor-pointer">
                                                <i class="fas fa-check text-xs"></i>
                                                <span>Approve Instructor</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif

                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                                <p class="text-sm font-bold text-[#0A0A0A]">All Caught Up!</p>
                                <p class="text-xs text-[#6B6B6B] mt-1">There are no pending registrations or instructor applications awaiting review.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-[#E5E5E5]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
