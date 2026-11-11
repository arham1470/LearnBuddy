<x-layouts.public title="Taking Quiz: {{ $quiz->title }} - LearnBuddy" description="Active quiz attempt question interface.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Quiz Attempt Header Bar -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-4 z-20 backdrop-blur-md bg-white/95">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-xs text-neutral-500 mb-1">
                        <span>{{ $course->title }}</span>
                        <span>•</span>
                        <span class="font-bold text-lb-orange">Attempt #{{ $attempt->attempt_number }}</span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-extrabold text-lb-black truncate">
                        {{ $quiz->title }}
                    </h1>
                </div>

                <div class="flex items-center gap-4 shrink-0 text-xs text-neutral-600">
                    <div class="flex items-center gap-2 bg-neutral-100 px-3.5 py-1.5 rounded-full font-semibold">
                        <i class="fas fa-list-ol text-lb-orange" aria-hidden="true"></i>
                        <span>{{ $questions->count() }} Questions</span>
                    </div>

                    @if ($quiz->time_limit_minutes)
                        <div class="flex items-center gap-2 bg-amber-50 text-amber-800 border border-amber-200 px-3.5 py-1.5 rounded-full font-bold">
                            <i class="fas fa-stopwatch text-amber-600" aria-hidden="true"></i>
                            <span>{{ $quiz->time_limit_minutes }}m limit</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quiz Questions Form -->
            <form
                action="{{ route('courses.quizzes.submit', [$course, $quiz, $attempt]) }}"
                method="POST"
                id="quiz-form"
                class="space-y-6"
                onsubmit="return confirm('Are you sure you want to finish and submit your answers?');"
            >
                @csrf

                @foreach ($questions as $index => $question)
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-7 h-7 rounded-full bg-neutral-900 text-white flex items-center justify-center text-xs font-extrabold shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <h2 class="text-sm sm:text-base font-bold text-lb-black leading-snug">
                                    {{ $question->question_text }}
                                </h2>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-neutral-100 text-neutral-600 shrink-0">
                                {{ $question->marks }} {{ Str::plural('mark', $question->marks) }}
                            </span>
                        </div>

                        <!-- Question Options -->
                        <div class="space-y-2.5 pt-2">
                            @foreach ($question->options as $option)
                                <label class="flex items-center gap-3 p-3.5 sm:p-4 rounded-2xl border border-neutral-200 hover:border-orange-300 hover:bg-orange-50/30 cursor-pointer transition text-xs sm:text-sm text-neutral-700 font-medium has-[:checked]:border-lb-orange has-[:checked]:bg-orange-50/50 has-[:checked]:text-lb-black">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $option->id }}"
                                        class="w-4 h-4 text-lb-orange focus:ring-lb-orange border-neutral-300"
                                    >
                                    <span class="leading-relaxed">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Submit Action Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-xs text-neutral-500">
                        <p class="font-semibold text-neutral-800">Ready to complete?</p>
                        <p>Once submitted, your answers will be automatically scored.</p>
                    </div>

                    <div class="flex items-center gap-3 justify-end">
                        <a
                            href="{{ route('courses.quizzes.show', [$course, $quiz]) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Save &amp; Return Later
                        </a>

                        <button
                            type="submit"
                            class="px-7 py-3 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-check-circle mr-1.5" aria-hidden="true"></i>
                            Submit Quiz
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>
