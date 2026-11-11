<x-layouts.public title="Student Attempts: {{ $quiz->title }} - LearnBuddy" description="View all student attempts and performance.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.quizzes.index', $course) }}" class="hover:text-lb-orange">Quizzes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $quiz->title }} Attempts</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">
                        Student Attempts
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Quiz: <strong class="text-neutral-700">{{ $quiz->title }}</strong> · Passing grade: {{ $quiz->passing_percentage }}%
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('management.courses.quizzes.index', $course) }}"
                        class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Back to Quizzes
                    </a>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($attempts->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Student</th>
                                    <th class="p-4">Attempt #</th>
                                    <th class="p-4">Date</th>
                                    <th class="p-4">Score</th>
                                    <th class="p-4">Result</th>
                                    <th class="p-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($attempts as $attempt)
                                    <tr class="hover:bg-neutral-50/50">
                                        <td class="p-4 font-bold text-neutral-900">
                                            {{ $attempt->user->name }}
                                            <div class="text-[11px] font-normal text-neutral-400">{{ $attempt->user->email }}</div>
                                        </td>
                                        <td class="p-4 font-bold text-neutral-800">
                                            #{{ $attempt->attempt_number }}
                                        </td>
                                        <td class="p-4 text-neutral-500">
                                            {{ $attempt->started_at->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}
                                        </td>
                                        <td class="p-4">
                                            @if ($attempt->isCompleted())
                                                <span class="font-extrabold text-neutral-900">{{ $attempt->score }} / {{ $attempt->total_marks }}</span>
                                                <span class="text-neutral-400 text-[10px]">({{ $attempt->percentage }}%)</span>
                                            @else
                                                <span class="text-amber-600 font-semibold">In Progress</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if ($attempt->isCompleted())
                                                @if ($attempt->is_passed)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                        Passed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                        Failed
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                                    In Progress
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right">
                                            @if ($attempt->isCompleted())
                                                <a
                                                    href="{{ route('courses.quizzes.result', [$course, $quiz, $attempt]) }}"
                                                    class="inline-flex items-center gap-1 text-lb-orange hover:underline font-semibold"
                                                >
                                                    <span>View Answers</span>
                                                    <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <span class="text-neutral-400 text-[11px]">Unsubmitted</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center text-xs text-neutral-500">
                        No students have attempted this quiz yet.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
