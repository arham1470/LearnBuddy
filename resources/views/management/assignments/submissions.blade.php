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
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/90 shadow-xs flex items-center justify-between gap-4 text-emerald-950"
                    role="status"
                >
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i class="fas fa-check text-sm" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Grading Update</h4>
                            <p class="text-xs sm:text-sm font-semibold text-emerald-900 mt-0.5 leading-snug">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="show = false"
                        class="w-7 h-7 rounded-lg text-emerald-600 hover:text-emerald-900 hover:bg-emerald-100/70 flex items-center justify-center transition-colors shrink-0"
                        aria-label="Dismiss message"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="p-4 rounded-2xl bg-rose-50/80 border border-rose-200/90 shadow-xs text-rose-950 space-y-3"
                    role="alert"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                                <i class="fas fa-triangle-exclamation text-sm" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Grading Error</h4>
                                <p class="text-xs font-semibold text-rose-900">Please correct the following issues:</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="show = false"
                            class="w-7 h-7 rounded-lg text-rose-600 hover:text-rose-900 hover:bg-rose-100/70 flex items-center justify-center transition-colors shrink-0"
                            aria-label="Dismiss errors"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <ul class="pl-13 space-y-1 text-xs text-rose-800 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-bold text-lb-orange uppercase tracking-wider mb-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200/60">
                            <i class="fas fa-file-signature text-[10px]"></i>
                            Assignment Submissions
                        </span>
                        <span class="text-neutral-300">•</span>
                        <span class="text-neutral-500 font-semibold lowercase">max {{ $assignment->max_points }} pts</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        {{ $assignment->title }}
                    </h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: <strong class="text-neutral-700">{{ $course->title }}</strong></p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a
                        href="{{ route('management.courses.assignments.index', $course) }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 hover:border-neutral-400 transition-colors shadow-2xs"
                    >
                        <i class="fas fa-arrow-left text-[11px]"></i>
                        <span>Back to Assignments</span>
                    </a>
                </div>
            </div>

            <!-- Submissions List -->
            <div class="space-y-4">
                @if ($submissions->isNotEmpty())
                    @foreach ($submissions as $submission)
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-7 space-y-5 transition-all hover:shadow-md">
                            <!-- Top Row: Student Profile & Status Badge -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-neutral-100">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-neutral-800 to-neutral-950 text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0 ring-4 ring-neutral-100">
                                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-extrabold text-neutral-900 text-sm sm:text-base leading-tight">
                                                {{ $submission->student->name }}
                                            </h3>
                                            @if ($submission->is_late)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                                    <i class="fas fa-clock text-[9px]"></i> Late
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-neutral-400 font-medium truncate mt-0.5">
                                            {{ $submission->student->email }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Status & Score Header Pill -->
                                <div class="flex items-center gap-2.5 shrink-0 self-start sm:self-center">
                                    @if ($submission->isGraded())
                                        <div class="flex items-center gap-2">
                                            <div class="text-right">
                                                <div class="text-[10px] uppercase font-bold tracking-wider text-neutral-400">Awarded Score</div>
                                                <div class="flex items-baseline gap-1 justify-end">
                                                    <span class="text-lg font-black {{ $submission->isPassing() ? 'text-emerald-600' : 'text-rose-600' }}">
                                                        {{ $submission->points_awarded }}
                                                    </span>
                                                    <span class="text-xs font-bold text-neutral-400">/ {{ $assignment->max_points }} pts</span>
                                                </div>
                                            </div>
                                            @if ($submission->isPassing())
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                                    <i class="fas fa-circle-check text-xs"></i> Passed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs">
                                                    <i class="fas fa-circle-exclamation text-xs"></i> Needs Revision
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                            <span>Needs Grading</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Middle: File Solution Card -->
                            <div class="bg-neutral-50/80 border border-neutral-200/70 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-neutral-50 transition-colors">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center shrink-0 shadow-2xs">
                                        <i class="fas fa-file-arrow-up text-base" aria-hidden="true"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs sm:text-sm font-bold text-neutral-900 block truncate">
                                            {{ $submission->file_original_name }}
                                        </span>
                                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-neutral-500 mt-0.5">
                                            <span>{{ $submission->formattedFileSize() }}</span>
                                            <span>•</span>
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-calendar-check text-[10px] text-neutral-400"></i>
                                                {{ $submission->submitted_at->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}
                                            </span>
                                            <span>•</span>
                                            <span>{{ $submission->submitted_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('assignments.submissions.download', $submission) }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-neutral-900 text-white hover:bg-lb-orange text-xs font-bold shadow-xs transition-colors shrink-0"
                                >
                                    <i class="fas fa-cloud-arrow-down text-xs" aria-hidden="true"></i>
                                    <span>Download Solution</span>
                                </a>
                            </div>

                            <!-- Student Note (if present) -->
                            @if ($submission->student_notes)
                                <div class="bg-orange-50/40 border border-orange-100/90 rounded-2xl p-3.5 text-xs flex items-start gap-3">
                                    <i class="fas fa-message text-lb-orange text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                                    <div class="min-w-0">
                                        <span class="font-bold text-neutral-800 block text-[11px] uppercase tracking-wider mb-0.5">Student Note</span>
                                        <p class="text-neutral-700 leading-relaxed whitespace-pre-line">{{ $submission->student_notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Bottom: Interactive Grading Console -->
                            <div class="pt-2">
                                <form action="{{ route('management.courses.assignments.grade', [$course, $assignment, $submission]) }}" method="POST" class="bg-gradient-to-b from-neutral-50/90 to-neutral-50/40 p-4 sm:p-5 rounded-2xl border border-neutral-200/80 space-y-4">
                                    @csrf

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-neutral-200/60">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg bg-orange-100 text-lb-orange flex items-center justify-center text-xs">
                                                <i class="fas fa-pen-fancy text-[10px]"></i>
                                            </span>
                                            <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider">
                                                {{ $submission->isGraded() ? 'Update Evaluation & Feedback' : 'Grade This Submission' }}
                                            </span>
                                        </div>

                                        @if ($submission->grader)
                                            <span class="text-[11px] text-neutral-400">
                                                Last reviewed by <strong class="text-neutral-600">{{ $submission->grader->name }}</strong> on {{ $submission->graded_at?->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-neutral-700 mb-1 uppercase tracking-wider">
                                                Score (Max {{ $assignment->max_points }}) <span class="text-rose-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    name="points_awarded"
                                                    value="{{ old('points_awarded', $submission->points_awarded) }}"
                                                    min="0"
                                                    max="{{ $assignment->max_points }}"
                                                    required
                                                    placeholder="e.g. 85"
                                                    class="w-full text-xs p-2.5 pl-8 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white font-bold text-neutral-900"
                                                >
                                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-neutral-400">
                                                    <i class="fas fa-award text-xs"></i>
                                                </div>
                                            </div>
                                            @if ($assignment->passing_points)
                                                <span class="text-[10px] text-neutral-400 mt-1 block">Pass mark: {{ $assignment->passing_points }} pts</span>
                                            @endif
                                        </div>

                                        <div class="sm:col-span-3">
                                            <label class="block text-[11px] font-bold text-neutral-700 mb-1 uppercase tracking-wider">
                                                Instructor Feedback &amp; Rubric Notes
                                            </label>
                                            <input
                                                type="text"
                                                name="feedback"
                                                value="{{ old('feedback', $submission->feedback) }}"
                                                placeholder="Provide constructive feedback, strengths, and areas to improve..."
                                                class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white text-neutral-800"
                                            >
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-[11px] text-neutral-400">
                                            Student will receive an instant notification with their score.
                                        </span>

                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-sm hover:shadow transition-all"
                                        >
                                            <i class="fas fa-check text-xs"></i>
                                            <span>{{ $submission->isGraded() ? 'Update Evaluation' : 'Confirm & Save Grade' }}</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center space-y-3">
                        <div class="w-16 h-16 rounded-3xl bg-orange-50 border border-orange-100 text-lb-orange flex items-center justify-center mx-auto text-2xl shadow-xs">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="text-base font-extrabold text-neutral-900">No Submissions Yet</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto leading-relaxed">
                            When enrolled students upload their coursework files, they will appear here ready for grading and instructor feedback.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
