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
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-[#0A0A0A] text-sm">{{ $user->name }}</p>
                                            @if($isInstructorApp)
                                                <button type="button"
                                                        @click="expandedId = (expandedId === {{ $user->id }} ? null : {{ $user->id }})"
                                                        class="text-[11px] font-bold text-[#FF5F00] hover:underline flex items-center gap-1 cursor-pointer">
                                                    <span>View Application</span>
                                                    <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" :class="{ 'rotate-180': expandedId === {{ $user->id }} }"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-xs text-[#6B6B6B]">{{ $user->email }}</p>
                                    </div>
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
                                    <form method="POST" action="{{ route('admin.approvals.approve', $user) }}" class="inline-block">
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
                                    <form method="POST" action="{{ route('admin.approvals.reject', $user) }}" class="inline-block">
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
                        <tr x-show="expandedId === {{ $user->id }}" x-cloak class="bg-[#FFFDF9] border-b border-[#F0ECE1]">
                            <td colspan="5" class="p-6 whitespace-normal">
                                <div class="max-w-4xl bg-white rounded-2xl border border-orange-200/70 p-5 shadow-xs space-y-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-orange-100 pb-3">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#FF5F00] uppercase tracking-wider">Professional Headline</span>
                                            <h4 class="text-base font-bold text-[#0A0A0A]">{{ $application->headline }}</h4>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($application->linkedin_url)
                                                <a href="{{ $application->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold flex items-center gap-1.5 transition">
                                                    <i class="fab fa-linkedin"></i>
                                                    <span>LinkedIn</span>
                                                </a>
                                            @endif
                                            @if($application->portfolio_url)
                                                <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="px-3 py-1.5 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-neutral-800 text-xs font-bold flex items-center gap-1.5 transition">
                                                    <i class="fas fa-globe"></i>
                                                    <span>Portfolio / Web</span>
                                                </a>
                                            @endif
                                            @if($application->sample_video_url)
                                                <a href="{{ $application->sample_video_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold flex items-center gap-1.5 transition">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>Watch Sample Lecture</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Teaching Bio & Experience -->
                                    <div>
                                        <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-1">Teaching Experience &amp; Bio</span>
                                        <p class="text-xs text-[#0A0A0A] leading-relaxed bg-[#F7F7F5] p-3.5 rounded-xl border border-[#E5E5E5]">
                                            {{ $application->bio }}
                                        </p>
                                    </div>

                                    <!-- Subject Areas & Languages -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-1.5">Subject Expertise</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach((array)($application->subject_areas ?? []) as $subject)
                                                    <span class="px-2.5 py-1 rounded-lg bg-orange-100 text-orange-800 font-bold text-xs">
                                                        {{ $subject }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div>
                                            <span class="text-[10px] font-bold text-[#6B6B6B] uppercase tracking-wider block mb-1.5">Teaching Languages</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach((array)($application->languages ?? []) as $lang)
                                                    <span class="px-2.5 py-1 rounded-lg bg-neutral-100 text-neutral-700 font-semibold text-xs capitalize">
                                                        {{ $lang }}
                                                    </span>
                                                @endforeach
                                            </div>
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
