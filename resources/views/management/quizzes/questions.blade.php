<x-layouts.public title="Questions: {{ $quiz->title }} - LearnBuddy" description="Manage questions, multiple choices, and correct answers.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.quizzes.index', $course) }}" class="hover:text-lb-orange">Quizzes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $quiz->title }} Questions</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Unable to add question:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-purple-700 mb-1">
                        <span>{{ $questions->count() }} Questions</span>
                        <span>•</span>
                        <span>Total {{ $quiz->totalMarks() }} Marks</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">
                        {{ $quiz->title }} Questions
                    </h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: {{ $course->title }}</p>
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

            <!-- Add New Question Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-5">
                <h2 class="text-base font-bold text-lb-black flex items-center gap-2">
                    <i class="fas fa-plus-circle text-lb-orange" aria-hidden="true"></i> Add New Question
                </h2>

                <form action="{{ route('management.courses.quizzes.questions.store', [$course, $quiz]) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="question_text" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Question Text <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            name="question_text"
                            id="question_text"
                            rows="2"
                            required
                            placeholder="Type the question or problem prompt..."
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >{{ old('question_text') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="question_type" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Question Type <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="question_type"
                                id="question_type"
                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white"
                            >
                                <option value="single_choice" {{ old('question_type') === 'single_choice' ? 'selected' : '' }}>Single Choice (Radio)</option>
                                <option value="true_false" {{ old('question_type') === 'true_false' ? 'selected' : '' }}>True / False</option>
                            </select>
                        </div>

                        <div>
                            <label for="marks" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Marks Awarded <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="marks"
                                id="marks"
                                value="{{ old('marks', 1) }}"
                                min="1"
                                max="100"
                                required
                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>

                        <div>
                            <label for="explanation" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Explanation (Optional)
                            </label>
                            <input
                                type="text"
                                name="explanation"
                                id="explanation"
                                value="{{ old('explanation') }}"
                                placeholder="Why this answer is correct..."
                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <!-- Options & Correct Answer Radio Selector -->
                    <div class="pt-3 border-t border-neutral-100 space-y-2">
                        <label class="block text-xs font-bold text-neutral-700 uppercase tracking-wider">
                            Options &amp; Correct Answer Selection <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-neutral-500">Provide options below and select the radio button corresponding to the correct answer.</p>

                        <div class="space-y-2 pt-1">
                            @for ($i = 0; $i < 4; $i++)
                                <div class="flex items-center gap-3 bg-neutral-50 p-2.5 rounded-xl border border-neutral-200">
                                    <input
                                        type="radio"
                                        name="correct_option_index"
                                        value="{{ $i }}"
                                        {{ (string) old('correct_option_index', '0') === (string) $i ? 'checked' : '' }}
                                        class="w-4 h-4 text-lb-orange focus:ring-lb-orange border-neutral-300 cursor-pointer shrink-0"
                                        title="Mark as correct answer"
                                    >
                                    <input
                                        type="text"
                                        name="options[{{ $i }}][text]"
                                        value="{{ old('options.' . $i . '.text') }}"
                                        placeholder="Option {{ $i + 1 }} {{ $i < 2 ? '(Required)' : '(Optional)' }}"
                                        {{ $i < 2 ? 'required' : '' }}
                                        class="flex-1 text-xs p-2 rounded-lg border border-neutral-300 bg-white focus:outline-none focus:ring-1 focus:ring-lb-orange"
                                    >
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus mr-1" aria-hidden="true"></i> Save Question
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Questions List -->
            <div class="space-y-4">
                <h2 class="text-base font-bold text-lb-black px-1">
                    Existing Questions ({{ $questions->count() }})
                </h2>

                @if ($questions->isNotEmpty())
                    @foreach ($questions as $index => $question)
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-full bg-neutral-900 text-white flex items-center justify-center text-xs font-extrabold shrink-0 mt-0.5">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-lb-black">
                                            {{ $question->question_text }}
                                        </h3>
                                        <span class="text-[11px] text-neutral-400">
                                            {{ ucfirst(str_replace('_', ' ', $question->question_type)) }} · {{ $question->marks }} {{ Str::plural('mark', $question->marks) }}
                                        </span>
                                    </div>
                                </div>

                                <form action="{{ route('management.courses.quizzes.questions.destroy', [$course, $quiz, $question]) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-semibold p-1">
                                        <i class="fas fa-trash-alt mr-1" aria-hidden="true"></i> Delete
                                    </button>
                                </form>
                            </div>

                            <!-- Options display -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 text-xs">
                                @foreach ($question->options as $opt)
                                    <div class="p-2.5 rounded-xl border flex items-center gap-2 {{ $opt->is_correct ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-semibold' : 'bg-neutral-50 border-neutral-200 text-neutral-600' }}">
                                        @if ($opt->is_correct)
                                            <i class="fas fa-check-circle text-emerald-600 shrink-0" aria-hidden="true"></i>
                                        @else
                                            <i class="far fa-circle text-neutral-300 shrink-0" aria-hidden="true"></i>
                                        @endif
                                        <span class="truncate">{{ $opt->option_text }}</span>
                                        @if ($opt->is_correct)
                                            <span class="ml-auto text-[9px] uppercase tracking-wider font-extrabold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Correct</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($question->explanation)
                                <div class="text-[11px] text-neutral-500 bg-orange-50/50 p-2.5 rounded-xl border border-orange-100">
                                    <span class="font-bold text-neutral-700">Explanation:</span> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-8 text-center text-xs text-neutral-500">
                        No questions added yet. Use the form above to add your first question.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
