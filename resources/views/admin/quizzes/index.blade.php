@extends('layouts.admin')

@section('title', 'Quizzes & Tests')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Quizzes &amp; Tests</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Manage multiple-choice questions, passing percentages, and student attempt results.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-white border border-[#E5E5E5] text-sm font-semibold text-[#0A0A0A]">
            {{ $totalAttempts }} Total Attempts
        </div>
    </div>

    <!-- Quizzes Table -->
    <div class="lb-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F7F7F5] border-b border-[#E5E5E5] text-[0.6875rem] font-bold uppercase tracking-wider text-[#6B6B6B]">
                        <th class="py-3 px-5">Quiz Title</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Questions</th>
                        <th class="py-3 px-4">Pass Score</th>
                        <th class="py-3 px-4">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @forelse($quizzes as $quiz)
                    <tr class="hover:bg-[#FFFDFB] transition-colors">
                        <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">{{ $quiz->title }}</td>
                        <td class="py-3.5 px-4 text-[#6B6B6B]">{{ $quiz->course->title ?? 'N/A' }}</td>
                        <td class="py-3.5 px-4 font-semibold text-[#0A0A0A]">{{ $quiz->questions->count() }} Qs</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#DEF7EC] text-[#03543F]">
                                {{ $quiz->pass_percentage ?? 70 }}%
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-[#6B6B6B]">
                            {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' mins' : 'Untimed' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-[#6B6B6B]">
                            <div class="w-12 h-12 rounded-2xl bg-[#FFF0E6] text-[#FF5F00] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="6" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="2" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="font-bold text-[#0A0A0A]">No quizzes created</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
