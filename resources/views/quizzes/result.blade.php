<x-layouts.public title="Quiz Results: {{ $quiz->title }} - LearnBuddy" description="Score breakdown and review for quiz attempt.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('courses.show', $course) }}" class="hover:text-lb-orange">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('courses.quizzes.show', [$course, $quiz]) }}" class="hover:text-lb-orange">{{ $quiz->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">Attempt #{{ $attempt->attempt_number }} Results</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Scorecard Hero -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 text-center space-y-6">
                <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center text-2xl {{ $attempt->is_passed ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                    <i class="fas {{ $attempt->is_passed ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
                </div>

                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $attempt->is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $attempt->is_passed ? 'Passed Quiz' : 'Did Not Pass' }}
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-black text-lb-black tracking-tight mt-2">
                        {{ $attempt->percentage }}%
                    </h1>
                    <p class="text-xs text-neutral-500">
                        Passing grade: {{ $quiz->passing_percentage }}% · You scored {{ $attempt->score }} out of {{ $attempt->total_marks }} marks
                    </p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-3 max-w-md mx-auto pt-4 border-t border-neutral-100 text-xs">
                    <div class="bg-neutral-50 rounded-2xl p-3">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Attempt</span>
                        <span class="font-extrabold text-neutral-800">#{{ $attempt->attempt_number }}</span>
                    </div>
                    <div class="bg-neutral-50 rounded-2xl p-3">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Time Taken</span>
                        <span class="font-extrabold text-neutral-800">{{ $attempt->timeSpentInMinutes() ?? 1 }} mins</span>
                    </div>
                    <div class="bg-neutral-50 rounded-2xl p-3">
                        <span class="text-neutral-400 block text-[10px] uppercase font-bold">Completed</span>
                        <span class="font-extrabold text-neutral-800">{{ $attempt->completed_at ? $attempt->completed_at->timezone('Asia/Colombo')->format('M d') : 'Just now' }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3 pt-4 border-t border-neutral-100">
                    <a
                        href="{{ route('learning.index', $course) }}"
                        class="px-5 py-2.5 rounded-full bg-neutral-900 text-white hover:bg-black font-semibold text-xs transition"
                    >
                        <i class="fas fa-arrow-left mr-1.5" aria-hidden="true"></i>
                        Back to Course
                    </a>

                    <a
                        href="{{ route('courses.quizzes.show', [$course, $quiz]) }}"
                        class="px-5 py-2.5 rounded-full border border-neutral-300 text-neutral-700 hover:bg-neutral-50 font-semibold text-xs transition"
                    >
                        All Attempts
                    </a>

                    @if ($quiz->canUserAttempt(auth()->user()))
                        <form action="{{ route('courses.quizzes.start', [$course, $quiz]) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-sm transition"
                            >
                                <i class="fas fa-redo mr-1" aria-hidden="true"></i> Retake Quiz
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Detailed Question Breakdown -->
            @if ($showAnswers)
                <div class="space-y-4">
                    <h2 class="text-base font-bold text-lb-black flex items-center gap-2 px-1">
                        <i class="fas fa-clipboard-check text-lb-orange" aria-hidden="true"></i> Review Answers
                    </h2>

                    @foreach ($attempt->answers as $index => $answer)
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-7 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        <i class="fas {{ $answer->is_correct ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
                                    </span>
                                    <h3 class="text-sm font-bold text-lb-black">
                                        Question {{ $index + 1 }}: {{ $answer->question->question_text }}
                                    </h3>
                                </div>

                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold shrink-0 {{ $answer->is_correct ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $answer->marks_awarded }} / {{ $answer->question->marks }} pts
                                </span>
                            </div>

                            <!-- Options Breakdown -->
                            <div class="space-y-2 pt-2 text-xs">
                                @foreach ($answer->question->options as $opt)
                                    @php
                                        $isSelected = ((int) $answer->selected_option_id === (int) $opt->id);
                                        $isCorrectOpt = (bool) $opt->is_correct;
                                    @endphp

                                    <div class="p-3 rounded-xl border flex items-center justify-between gap-3
                                        @if ($isCorrectOpt)
                                            bg-emerald-50/80 border-emerald-300 text-emerald-900 font-semibold
                                        @elseif ($isSelected && ! $isCorrectOpt)
                                            bg-rose-50/80 border-rose-300 text-rose-900 line-through
                                        @else
                                            bg-neutral-50 border-neutral-200 text-neutral-600
                                        @endif
                                    ">
                                        <div class="flex items-center gap-2">
                                            @if ($isCorrectOpt)
                                                <i class="fas fa-check-circle text-emerald-600 shrink-0" aria-hidden="true"></i>
                                            @elseif ($isSelected)
                                                <i class="fas fa-times-circle text-rose-600 shrink-0" aria-hidden="true"></i>
                                            @else
                                                <i class="far fa-circle text-neutral-300 shrink-0" aria-hidden="true"></i>
                                            @endif
                                            <span>{{ $opt->option_text }}</span>
                                        </div>

                                        <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                            @if ($isSelected)
                                                <span class="px-2 py-0.5 rounded-full bg-neutral-800 text-white">Your Answer</span>
                                            @endif
                                            @if ($isCorrectOpt)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-600 text-white">Correct</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($answer->question->explanation)
                                <div class="mt-3 p-3.5 bg-orange-50/60 border border-orange-200/70 rounded-xl text-xs text-neutral-700 leading-relaxed">
                                    <span class="font-bold text-orange-900 block mb-0.5">Explanation:</span>
                                    {{ $answer->question->explanation }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 text-center text-xs text-neutral-500">
                    <i class="fas fa-eye-slash text-neutral-400 text-base mb-2 block" aria-hidden="true"></i>
                    Answer explanations and solutions are not disclosed for this assessment.
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
