<x-layouts.public title="Submissions: {{ $assignment->title }} - LearnBuddy" description="Review and grade student submissions.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.assignments.index', $course) }}" class="hover:text-lb-orange">Assignments</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $assignment->title }} Submissions</span>
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
                        <p class="font-semibold">Grading action failed:</p>
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
                    <div class="flex items-center gap-2 text-xs font-semibold text-lb-orange mb-1">
                        <span>Assignment Submissions</span>
                        <span>•</span>
                        <span>Max {{ $assignment->max_points }} Points</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">
                        {{ $assignment->title }}
                    </h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: {{ $course->title }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('management.courses.assignments.index', $course) }}"
                        class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Back to Assignments
                    </a>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="space-y-4">
                @if ($submissions->isNotEmpty())
                    @foreach ($submissions as $submission)
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-4">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <!-- Student & File Details -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-neutral-900 text-sm">{{ $submission->student->name }}</span>
                                            <span class="text-xs text-neutral-400 ml-1">({{ $submission->student->email }})</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 text-xs text-neutral-600">
                                        <span class="inline-flex items-center gap-1 font-semibold text-neutral-800">
                                            <i class="fas fa-file text-lb-orange" aria-hidden="true"></i>
                                            {{ $submission->file_original_name }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $submission->formattedFileSize() }}</span>
                                        <span>•</span>
                                        <span>Submitted {{ $submission->submitted_at->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}</span>
                                        @if ($submission->is_late)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Late</span>
                                        @endif
                                    </div>

                                    @if ($submission->student_notes)
                                        <div class="text-xs text-neutral-600 bg-neutral-50 p-3 rounded-xl border border-neutral-100">
                                            <span class="font-bold text-neutral-700">Student Note:</span> {{ $submission->student_notes }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Download Button & Current Score -->
                                <div class="flex flex-wrap items-center md:items-end gap-3 shrink-0">
                                    @if ($submission->isGraded())
                                        <div class="text-right">
                                            <span class="text-xs text-neutral-400 block font-semibold">Score Awarded</span>
                                            <span class="text-lg font-extrabold text-emerald-600">
                                                {{ $submission->points_awarded }} / {{ $assignment->max_points }} pts
                                            </span>
                                        </div>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            Pending Grade
                                        </span>
                                    @endif

                                    <a
                                        href="{{ route('assignments.submissions.download', $submission) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-neutral-900 text-white hover:bg-black text-xs font-semibold transition"
                                    >
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Grading Form Area -->
                            <div class="pt-4 border-t border-neutral-100">
                                <form action="{{ route('management.courses.assignments.grade', [$course, $assignment, $submission]) }}" method="POST" class="bg-neutral-50 p-4 rounded-2xl border border-neutral-200/60 space-y-3">
                                    @csrf

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider">
                                            <i class="fas fa-pen text-lb-orange mr-1" aria-hidden="true"></i>
                                            {{ $submission->isGraded() ? 'Update Grade & Feedback' : 'Grade Submission' }}
                                        </span>
                                        @if ($submission->grader)
                                            <span class="text-[10px] text-neutral-400">
                                                Last graded by {{ $submission->grader->name }} on {{ $submission->graded_at?->timezone('Asia/Colombo')->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-neutral-600 mb-1">
                                                Score (Max {{ $assignment->max_points }}) <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                name="points_awarded"
                                                value="{{ old('points_awarded', $submission->points_awarded) }}"
                                                min="0"
                                                max="{{ $assignment->max_points }}"
                                                required
                                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white"
                                            >
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block text-[11px] font-bold text-neutral-600 mb-1">
                                                Feedback to Student (Optional)
                                            </label>
                                            <input
                                                type="text"
                                                name="feedback"
                                                value="{{ old('feedback', $submission->feedback) }}"
                                                placeholder="Provide constructive feedback and rubric comments..."
                                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-1">
                                        <button
                                            type="submit"
                                            class="px-5 py-2 rounded-xl bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-sm transition"
                                        >
                                            {{ $submission->isGraded() ? 'Update Grade' : 'Save Grade' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-12 text-center space-y-2">
                        <i class="fas fa-inbox text-neutral-300 text-3xl mb-2 block" aria-hidden="true"></i>
                        <h3 class="text-sm font-bold text-neutral-800">No Submissions Yet</h3>
                        <p class="text-xs text-neutral-500">
                            When enrolled students upload their coursework files, they will appear here for grading.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
