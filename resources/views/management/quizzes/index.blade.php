<x-layouts.public title="Course Quizzes (Management) - LearnBuddy" description="Manage course quizzes, question banks, and student attempt results.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange truncate">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Quizzes</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">
                        Course Quizzes
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Build multiple-choice and timed quizzes for <strong class="text-neutral-700">{{ $course->title }}</strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('management.courses.show', $course) }}"
                        class="px-4 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition"
                    >
                        Back to Course
                    </a>

                    <a
                        href="{{ route('management.courses.quizzes.create', $course) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                    >
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>New Quiz</span>
                    </a>
                </div>
            </div>

            <!-- Quizzes Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($quizzes->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Quiz Title</th>
                                    <th class="p-4">Questions</th>
                                    <th class="p-4">Passing %</th>
                                    <th class="p-4">Time Limit</th>
                                    <th class="p-4">Attempts Taken</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($quizzes as $quiz)
                                    <tr class="hover:bg-neutral-50/50">
                                        <td class="p-4">
                                            <div class="font-bold text-neutral-900 text-sm">{{ $quiz->title }}</div>
                                            <div class="text-[11px] text-neutral-400">
                                                Allowed: {{ $quiz->max_attempts === 0 ? 'Unlimited' : $quiz->max_attempts . ' attempts' }}
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <a
                                                href="{{ route('management.courses.quizzes.questions', [$course, $quiz]) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 font-bold hover:bg-purple-100 transition"
                                            >
                                                <i class="fas fa-list-ol text-[10px]" aria-hidden="true"></i>
                                                <span>{{ $quiz->questions_count }} questions</span>
                                            </a>
                                        </td>
                                        <td class="p-4 font-bold text-neutral-800">
                                            {{ $quiz->passing_percentage }}%
                                        </td>
                                        <td class="p-4 font-semibold text-neutral-700">
                                            {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' mins' : 'Untimed' }}
                                        </td>
                                        <td class="p-4">
                                            <a
                                                href="{{ route('management.courses.quizzes.attempts', [$course, $quiz]) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-neutral-100 text-neutral-700 font-semibold hover:bg-neutral-200"
                                            >
                                                <i class="fas fa-users text-[10px]" aria-hidden="true"></i>
                                                <span>{{ $quiz->attempts_count }} attempts</span>
                                            </a>
                                        </td>
                                        <td class="p-4">
                                            @if ($quiz->is_published)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                    Published
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-600">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <a
                                                href="{{ route('management.courses.quizzes.questions', [$course, $quiz]) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl bg-orange-50 text-lb-orange hover:bg-orange-100 font-bold"
                                            >
                                                Questions
                                            </a>

                                            <a
                                                href="{{ route('management.courses.quizzes.edit', [$course, $quiz]) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 font-semibold"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                data-item="{{ $quiz->title }}"
                                                data-action="{{ route('management.courses.quizzes.destroy', [$course, $quiz]) }}"
                                                onclick="confirmDialog({
                                                    title: 'Delete Quiz',
                                                    itemName: this.dataset.item,
                                                    message: 'Are you sure you want to delete this quiz? All questions, student attempts, and score histories will be permanently deleted.',
                                                    action: this.dataset.action,
                                                    method: 'DELETE',
                                                    confirmText: 'Delete Quiz',
                                                    confirmVariant: 'danger'
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-semibold cursor-pointer"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto text-xl">
                            <i class="fas fa-question-circle" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-sm font-bold text-neutral-800">No Quizzes Created Yet</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                            Create timed or untimed quizzes with instant scoring and question explanations.
                        </p>
                        <a
                            href="{{ route('management.courses.quizzes.create', $course) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Create First Quiz</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
