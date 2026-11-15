<x-layouts.public title="{{ $assignment->title }} - LearnBuddy" description="Assignment details and submission portal.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('courses.show', $course) }}" class="hover:text-lb-orange">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('learning.index', $course) }}" class="hover:text-lb-orange">Learning Portal</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $assignment->title }}</span>
            </div>

            <!-- Flash Status Messages -->
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
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Status Update</h4>
                            <p class="text-xs sm:text-sm font-semibold text-emerald-900 mt-0.5 leading-snug">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200/80">
                            <i class="fas fa-circle-check text-[9px]"></i> Success
                        </span>
                        <button
                            type="button"
                            @click="show = false"
                            class="w-7 h-7 rounded-lg text-emerald-600 hover:text-emerald-900 hover:bg-emerald-100/70 flex items-center justify-center transition-colors"
                            aria-label="Dismiss message"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="p-4 rounded-2xl bg-rose-50/80 border border-rose-200/90 shadow-xs flex items-center justify-between gap-4 text-rose-950"
                    role="alert"
                >
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs">
                            <i class="fas fa-exclamation text-sm" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Notice</h4>
                            <p class="text-xs sm:text-sm font-semibold text-rose-900 mt-0.5 leading-snug">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="show = false"
                        class="w-7 h-7 rounded-lg text-rose-600 hover:text-rose-900 hover:bg-rose-100/70 flex items-center justify-center transition-colors shrink-0"
                        aria-label="Dismiss error"
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
                                <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Submission Error</h4>
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

            <!-- Assignment Info Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-lb-orange">
                                <i class="fas fa-file-alt text-[10px]" aria-hidden="true"></i> Assignment
                            </span>
                            @if ($assignment->isPastDue())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                    <i class="fas fa-clock text-[10px]" aria-hidden="true"></i> Deadline Passed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-calendar-check text-[10px]" aria-hidden="true"></i> Open for Submission
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                            {{ $assignment->title }}
                        </h1>
                        @if ($assignment->description)
                            <p class="text-sm text-neutral-600 leading-relaxed">
                                {{ $assignment->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Meta Badges -->
                    <div class="flex sm:flex-col items-end gap-2 text-xs text-neutral-500 shrink-0">
                        <div class="bg-neutral-50 border border-neutral-200 rounded-2xl px-4 py-2.5 text-right w-full sm:w-auto">
                            <div class="text-[11px] text-neutral-400 uppercase font-bold tracking-wider">Total Marks</div>
                            <div class="text-lg font-extrabold text-lb-black">{{ $assignment->max_points }} pts</div>
                            @if ($assignment->passing_points)
                                <div class="text-[10px] text-neutral-500">Pass: {{ $assignment->passing_points }} pts</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Assignment Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-5 border-t border-neutral-100 text-xs">
                    <div class="bg-neutral-50/80 hover:bg-neutral-50 border border-neutral-200/70 rounded-2xl p-4 flex items-start gap-3 transition-colors">
                        <div class="w-9 h-9 rounded-xl bg-orange-100/70 text-lb-orange flex items-center justify-center shrink-0">
                            <i class="fas fa-calendar-day text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-neutral-400 block font-semibold text-[11px] uppercase tracking-wider mb-0.5">Due Date</span>
                            @if ($assignment->due_date)
                                <span class="font-bold text-neutral-800 block text-xs leading-tight">{{ $assignment->due_date->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}</span>
                                <span class="inline-block text-[10px] font-medium text-neutral-500 mt-1 px-1.5 py-0.5 rounded-md bg-neutral-100">
                                    {{ $assignment->due_date->diffForHumans() }}
                                </span>
                            @else
                                <span class="font-bold text-neutral-800 block text-xs">No strict deadline</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-neutral-50/80 hover:bg-neutral-50 border border-neutral-200/70 rounded-2xl p-4 flex items-start gap-3 transition-colors">
                        <div class="w-9 h-9 rounded-xl bg-sky-100/70 text-sky-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-file-code text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-neutral-400 block font-semibold text-[11px] uppercase tracking-wider mb-0.5">Permitted Files</span>
                            <span class="font-bold text-neutral-800 uppercase block text-xs truncate">{{ implode(', ', $assignment->allowedFileTypesArray()) }}</span>
                            <span class="inline-block text-[10px] text-neutral-500 mt-1">Direct upload</span>
                        </div>
                    </div>

                    <div class="bg-neutral-50/80 hover:bg-neutral-50 border border-neutral-200/70 rounded-2xl p-4 flex items-start gap-3 transition-colors">
                        <div class="w-9 h-9 rounded-xl bg-purple-100/70 text-purple-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-hard-drive text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-neutral-400 block font-semibold text-[11px] uppercase tracking-wider mb-0.5">Max File Size</span>
                            <span class="font-bold text-neutral-800 block text-xs">{{ $assignment->max_file_size_mb }} MB limit</span>
                            <span class="inline-block text-[10px] text-neutral-500 mt-1">Per submission</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Instructions -->
                @if ($assignment->instructions)
                    <div class="space-y-3 pt-4 border-t border-neutral-100">
                        <h2 class="text-sm font-bold text-neutral-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-list-check text-lb-orange" aria-hidden="true"></i> Instructions
                        </h2>
                        <div class="prose prose-sm max-w-none text-neutral-700 leading-relaxed bg-neutral-50/70 p-5 rounded-2xl border border-neutral-100 whitespace-pre-line text-xs sm:text-sm">
                            {{ $assignment->instructions }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submission Area -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-lb-black flex items-center gap-2">
                        <i class="fas fa-upload text-lb-orange" aria-hidden="true"></i> Your Submission
                    </h2>

                    @if ($submission)
                        @if ($submission->isGraded())
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                <i class="fas fa-award mr-1" aria-hidden="true"></i> Graded
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                <i class="fas fa-clock mr-1" aria-hidden="true"></i> Submitted (Awaiting Review)
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">
                            Not Submitted Yet
                        </span>
                    @endif
                </div>

                <!-- Active Submission Details -->
                @if ($submission)
                    <div class="bg-neutral-50 rounded-2xl p-5 border border-neutral-200/70 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center shrink-0">
                                    <i class="fas fa-file text-lg" aria-hidden="true"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-neutral-800 truncate">{{ $submission->file_original_name }}</p>
                                    <p class="text-xs text-neutral-500">
                                        {{ $submission->formattedFileSize() }} · Submitted on {{ $submission->submitted_at->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}
                                        @if ($submission->is_late)
                                            <span class="text-rose-600 font-bold ml-1">(Late)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('assignments.submissions.download', $submission) }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-neutral-900 text-white hover:bg-black text-xs font-semibold transition shrink-0"
                            >
                                <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                <span>Download File</span>
                            </a>
                        </div>

                        @if ($submission->student_notes)
                            <div class="text-xs text-neutral-600 pt-2 border-t border-neutral-200/60">
                                <span class="font-bold text-neutral-700">Notes:</span> {{ $submission->student_notes }}
                            </div>
                        @endif

                        <!-- Grade & Feedback Section -->
                        @if ($submission->isGraded())
                            <div class="mt-4 pt-4 border-t border-neutral-200 bg-white rounded-xl p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-neutral-600 uppercase tracking-wider">Instructor Grade</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl font-extrabold {{ $submission->isPassing() ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $submission->points_awarded }} / {{ $assignment->max_points }} pts
                                        </span>
                                        @if ($submission->isPassing())
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">PASSED</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">NEEDS WORK</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($submission->feedback)
                                    <div class="text-xs text-neutral-700 pt-2 border-t border-neutral-100">
                                        <span class="font-bold text-neutral-800 block mb-1">Feedback:</span>
                                        <div class="p-3 bg-neutral-50 rounded-lg whitespace-pre-line leading-relaxed">
                                            {{ $submission->feedback }}
                                        </div>
                                    </div>
                                @endif

                                @if ($submission->grader)
                                    <div class="text-[11px] text-neutral-400 text-right">
                                        Reviewed by {{ $submission->grader->name }} on {{ $submission->graded_at?->timezone('Asia/Colombo')->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Upload Form (Visible if not submitted OR if submitted but not yet graded and submission allowed) -->
                @if ((! $submission || ! $submission->isGraded()) && $assignment->canAcceptSubmission())
                    <form action="{{ route('courses.assignments.submit', [$course, $assignment]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            <label for="file" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider">
                                {{ $submission ? 'Replace Submission File' : 'Upload Solution File' }} <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="file"
                                name="file"
                                id="file"
                                required
                                class="block w-full text-xs text-neutral-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-lb-orange hover:file:bg-orange-100 border border-neutral-300 rounded-2xl cursor-pointer bg-neutral-50 p-2"
                            >
                            <p class="text-[11px] text-neutral-500">
                                Formats: {{ implode(', ', $assignment->allowedFileTypesArray()) }} · Max size: {{ $assignment->max_file_size_mb }} MB
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label for="student_notes" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider">
                                Submission Notes / Comments (Optional)
                            </label>
                            <textarea
                                name="student_notes"
                                id="student_notes"
                                rows="3"
                                placeholder="Add any comments or notes for your instructor..."
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white"
                            >{{ old('student_notes', $submission?->student_notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="submit"
                                class="px-6 py-3 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                            >
                                <i class="fas fa-paper-plane mr-1.5" aria-hidden="true"></i>
                                {{ $submission ? 'Update Submission' : 'Submit Assignment' }}
                            </button>
                        </div>
                    </form>
                @elseif (! $assignment->canAcceptSubmission() && ! $submission)
                    <div class="p-6 bg-neutral-100 rounded-2xl text-center text-xs text-neutral-600">
                        <i class="fas fa-lock text-neutral-400 text-lg mb-2 block" aria-hidden="true"></i>
                        The deadline has passed and late submissions are not allowed for this assignment.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
