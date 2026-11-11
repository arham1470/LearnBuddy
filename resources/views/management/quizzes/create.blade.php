<x-layouts.public title="Create Quiz - LearnBuddy" description="Create a new quiz for the course.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.quizzes.index', $course) }}" class="hover:text-lb-orange">Quizzes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">New Quiz</span>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Please correct the following errors:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">Create Quiz</h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: <strong class="text-neutral-700">{{ $course->title }}</strong></p>
                </div>

                <form action="{{ route('management.courses.quizzes.store', $course) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Quiz Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="e.g. AWS Core Services Knowledge Check"
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Short Description
                        </label>
                        <input
                            type="text"
                            name="description"
                            id="description"
                            value="{{ old('description') }}"
                            placeholder="Brief description of topics assessed..."
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="instructions" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Instructions for Students
                        </label>
                        <textarea
                            name="instructions"
                            id="instructions"
                            rows="4"
                            placeholder="Any advice or rules students should know before attempting..."
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >{{ old('instructions') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="passing_percentage" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Passing % <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="passing_percentage"
                                id="passing_percentage"
                                value="{{ old('passing_percentage', 70) }}"
                                min="1"
                                max="100"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="time_limit_minutes" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Time Limit (Minutes)
                            </label>
                            <input
                                type="number"
                                name="time_limit_minutes"
                                id="time_limit_minutes"
                                value="{{ old('time_limit_minutes') }}"
                                placeholder="Leave empty for untimed"
                                min="1"
                                max="360"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="max_attempts" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Max Attempts <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_attempts"
                                id="max_attempts"
                                value="{{ old('max_attempts', 1) }}"
                                min="0"
                                max="100"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                            <span class="text-[10px] text-neutral-400">Set 0 for unlimited attempts</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Publish Quiz immediately</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="show_correct_answers_after_attempt" value="1" {{ old('show_correct_answers_after_attempt', true) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Reveal correct answers and explanations after submission</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-neutral-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('management.courses.quizzes.index', $course) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            Create Quiz &amp; Add Questions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
