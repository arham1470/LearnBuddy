<x-layouts.public title="{{ $quiz->title }} - LearnBuddy" description="Quiz overview, instructions, and attempt history.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('courses.show', $course) }}" class="hover:text-lb-orange">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('learning.index', $course) }}" class="hover:text-lb-orange">Learning Portal</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $quiz->title }}</span>
            </div>

            <!-- Flash Alerts -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Quiz Overview Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                <i class="fas fa-question-circle text-[10px]" aria-hidden="true"></i> Quiz
                            </span>
                            @if ($quiz->is_published)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-check text-[10px]" aria-hidden="true"></i> Available
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">
                                    Draft / Hidden
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                            {{ $quiz->title }}
                        </h1>
                        @if ($quiz->description)
                            <p class="text-sm text-neutral-600 leading-relaxed">
                                {{ $quiz->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Best Score Badge if available -->
                    @if ($bestAttempt)
                        <div class="bg-orange-50 border border-orange-200 rounded-2xl px-5 py-3 text-right shrink-0">
                            <div class="text-[10px] text-orange-600 uppercase font-extrabold tracking-wider">Your Best Score</div>
                            <div class="text-xl font-black text-lb-orange">{{ $bestAttempt->percentage }}%</div>
                            <div class="text-[11px] font-semibold {{ $bestAttempt->is_passed ? 'text-emerald-700' : 'text-neutral-500' }}">
                                {{ $bestAttempt->is_passed ? 'Passed' : 'Not Passed' }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Parameters Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-neutral-100 text-xs">
                    <div class="bg-neutral-50 rounded-2xl p-4">
                        <span class="text-neutral-400 block font-semibold mb-1">Total Questions</span>
                        <span class="font-bold text-neutral-800 text-sm">{{ $quiz->questions()->count() }}</span>
                    </div>
                    <div class="bg-neutral-50 rounded-2xl p-4">
                        <span class="text-neutral-400 block font-semibold mb-1">Total Marks</span>
                        <span class="font-bold text-neutral-800 text-sm">{{ $quiz->totalMarks() }} pts</span>
                    </div>
                    <div class="bg-neutral-50 rounded-2xl p-4">
                        <span class="text-neutral-400 block font-semibold mb-1">Passing Mark</span>
                        <span class="font-bold text-neutral-800 text-sm">{{ $quiz->passing_percentage }}%</span>
                    </div>
                    <div class="bg-neutral-50 rounded-2xl p-4">
                        <span class="text-neutral-400 block font-semibold mb-1">Time Limit</span>
                        <span class="font-bold text-neutral-800 text-sm">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' mins' : 'Untimed' }}</span>
                    </div>
                </div>

                <!-- Instructions -->
                @if ($quiz->instructions)
                    <div class="space-y-2 pt-4 border-t border-neutral-100">
                        <h2 class="text-xs font-bold text-neutral-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-lb-orange" aria-hidden="true"></i> Instructions & Rules
                        </h2>
                        <div class="text-xs text-neutral-600 bg-neutral-50 p-4 rounded-2xl border border-neutral-100 leading-relaxed whitespace-pre-line">
                            {{ $quiz->instructions }}
                        </div>
                    </div>
                @endif

                <!-- Attempt Limits & Call to Action -->
                <div class="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs text-neutral-500">
                        <span class="font-semibold text-neutral-700">Attempts:</span>
                        Used {{ $attempts->count() }} of {{ $quiz->max_attempts === 0 ? 'Unlimited' : $quiz->max_attempts }} allowed attempts.
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($activeAttempt)
                            <a
                                href="{{ route('courses.quizzes.take', [$course, $quiz, $activeAttempt]) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-amber-500 text-white hover:bg-amber-600 font-bold text-xs shadow-md transition"
                            >
                                <i class="fas fa-play text-xs" aria-hidden="true"></i>
                                <span>Resume Attempt (#{{ $activeAttempt->attempt_number }})</span>
                            </a>
                        @elseif ($canAttempt)
                            <form action="{{ route('courses.quizzes.start', [$course, $quiz]) }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                                >
                                    <i class="fas fa-play text-xs" aria-hidden="true"></i>
                                    <span>{{ $attempts->count() > 0 ? 'Retake Quiz' : 'Start Quiz' }}</span>
                                </button>
                            </form>
                        @else
                            <div class="px-4 py-2.5 rounded-full bg-neutral-100 text-neutral-500 text-xs font-semibold">
                                <i class="fas fa-ban mr-1" aria-hidden="true"></i> Attempt Limit Reached
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attempt History Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                <h2 class="text-base font-bold text-lb-black flex items-center gap-2">
                    <i class="fas fa-history text-neutral-500" aria-hidden="true"></i> Past Attempts
                </h2>

                @if ($attempts->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-3">Attempt</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Score</th>
                                    <th class="p-3">Result</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($attempts as $att)
                                    <tr class="hover:bg-neutral-50/50">
                                        <td class="p-3 font-bold text-neutral-800">#{{ $att->attempt_number }}</td>
                                        <td class="p-3 text-neutral-500">
                                            {{ $att->created_at->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}
                                        </td>
                                        <td class="p-3">
                                            @if ($att->isCompleted())
                                                <span class="font-bold text-neutral-900">{{ $att->score }} / {{ $att->total_marks }}</span>
                                                <span class="text-neutral-400 text-[10px]">({{ $att->percentage }}%)</span>
                                            @else
                                                <span class="text-amber-600 font-semibold">In Progress</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            @if ($att->isCompleted())
                                                @if ($att->is_passed)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                        Passed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                        Failed
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                                    In Progress
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right">
                                            @if ($att->isCompleted())
                                                <a
                                                    href="{{ route('courses.quizzes.result', [$course, $quiz, $att]) }}"
                                                    class="inline-flex items-center gap-1 text-lb-orange hover:underline font-semibold"
                                                >
                                                    <span>View Result</span>
                                                    <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a
                                                    href="{{ route('courses.quizzes.take', [$course, $quiz, $att]) }}"
                                                    class="inline-flex items-center gap-1 text-amber-600 hover:underline font-semibold"
                                                >
                                                    <span>Resume</span>
                                                    <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-neutral-500 py-4 text-center">
                        You have not attempted this quiz yet. Click "Start Quiz" when you're ready!
                    </p>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
