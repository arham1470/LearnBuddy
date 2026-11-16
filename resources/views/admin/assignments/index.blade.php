@extends('layouts.admin')

@section('title', 'Assessments & Assignments')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Assessments &amp; Assignments</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review student task submissions, grading rubrics, and feedback.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-[#FFF0E6] border border-orange-200 text-[#FF5F00] font-bold text-sm">
            {{ $pendingSubmissionsCount }} Pending Reviews
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Assignment</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Max Score</th>
                        <th class="py-3 px-4">Submissions</th>
                        <th class="py-3 px-4">Due Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($assignments as $assignment)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">{{ $assignment->title }}</td>
                        <td class="py-3.5 px-4 text-[#6B6B6B]">{{ $assignment->course->title ?? 'N/A' }}</td>
                        <td class="py-3.5 px-4 font-semibold text-[#0A0A0A]">{{ $assignment->max_score ?? 100 }} pts</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EBF5FF] text-[#2B6CB0]">
                                {{ $assignment->submissions->count() }} submitted
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-[#6B6B6B]">
                            {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No deadline' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No assignments recorded</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
