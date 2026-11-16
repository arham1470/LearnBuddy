@extends('layouts.admin')

@section('title', 'Pending Student Approvals')

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
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Student Registration Approvals</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review newly registered students whose emails have been verified with 6-digit OTP codes and approve access.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors">
                Users Directory
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Awaiting Approval -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Awaiting Approval</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending_count'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Verified OTP, awaiting admin review</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Total Approved -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Total Approved Users</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($stats['total_approved'] ?? 0) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Active platform accounts</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.approvals.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:max-w-md">
                <svg class="w-4 h-4 text-[#6B6B6B] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search student name, email, or phone..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-[#1A1A1A] hover:bg-black text-white text-sm font-semibold transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.approvals.index') }}" class="px-4 py-2 rounded-xl border border-[#E5E5E5] text-[#6B6B6B] hover:text-[#0A0A0A] text-sm font-medium transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Pending Users Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Student</th>
                        <th class="py-3 px-4">Contact Phone</th>
                        <th class="py-3 px-4">Email Verified</th>
                        <th class="py-3 px-4">Registered On</th>
                        <th class="py-3 px-5 text-right">Review Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse ($users as $user)
                        <tr class="hover:bg-[#F7F7F5]/60 transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#1A1A1A] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#0A0A0A] text-sm">{{ $user->name }}</p>
                                        <p class="text-xs text-[#6B6B6B]">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-[#6B6B6B]">
                                {{ $user->phone ?? 'Not provided' }}
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Verified via OTP</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-[#6B6B6B]">
                                {{ $user->created_at->format('M d, Y · h:i A') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.approvals.approve', $user) }}" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                            Approve Access
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.approvals.reject', $user) }}" onsubmit="return confirm('Are you sure you want to reject and remove this student registration?');" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-transparent hover:border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors cursor-pointer">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="font-bold text-[#0A0A0A]">All Caught Up!</div>
                                <div class="text-xs text-[#6B6B6B] mt-1">There are no pending student registrations awaiting approval at this time.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-[#E5E5E5] bg-[#F7F7F5]/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
