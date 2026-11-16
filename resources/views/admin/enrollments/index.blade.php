@extends('layouts.admin')

@section('title', 'Course Enrollments')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Student Enrollments</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review student admissions, delivery modes, and payment fulfillment.</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-7">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by student name, email, or course title..."
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
            </div>
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#1A1A1A] hover:bg-black text-white text-sm font-semibold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Enrollments Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Student</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Mode</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 text-right">Enrolled At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($enrollments as $enr)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#FFF0E6] text-[#FF5F00] font-bold text-xs flex items-center justify-center shrink-0 border border-orange-200">
                                    {{ strtoupper(substr($enr->user->name ?? 'ST', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-[#0A0A0A]">{{ $enr->user->name ?? 'Unknown Student' }}</div>
                                    <div class="text-xs text-[#6B6B6B]">{{ $enr->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-[#0A0A0A] max-w-xs truncate" title="{{ $enr->course->title ?? '' }}">
                            {{ $enr->course->title ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EBF5FF] text-[#2B6CB0]">
                                {{ ucfirst($enr->delivery_mode ?? 'Online') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($enr->status === 'active')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">Active</span>
                            @elseif($enr->status === 'completed')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F3F4F6] text-[#4B5563]">Completed</span>
                            @elseif($enr->status === 'pending_payment')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FEF3C7] text-[#92400E]">Pending Payment</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($enr->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-right text-xs text-[#6B6B6B] font-medium">
                            {{ $enr->created_at ? $enr->created_at->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.24.09-.367.09a.747.747 0 0 1-.367-.09l-.003-.001a49.95 49.95 0 0 0-9.902-3.913.75.75 0 0 1-.23-1.337A60.65 60.65 0 0 1 11.7 2.805Z" />
                                    <path d="M13.06 15.473a48.455 48.455 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.456.711l-7.25 3.107a.75.75 0 0 1-.59 0l-7.25-3.107a.75.75 0 0 1-.456-.711c.035-1.441.121-2.87.255-4.284a48.455 48.455 0 0 1 7.666 3.282.75.75 0 0 0 .59 0l.085-.036.085.036Z" />
                                </svg>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No enrollments found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
        <div class="p-4 border-t border-[#E5E5E5]">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
