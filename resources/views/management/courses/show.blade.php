<x-layouts.public title="{{ $course->title }} (Management) - LearnBuddy" description="Course management console, curriculum authoring, and publication lifecycle.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">{{ $course->title }}</span>
            </div>

            <!-- Status Flash Alerts -->
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
                        <p class="font-semibold">Action could not be completed:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Course Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="flex flex-col sm:flex-row items-start gap-5">
                        @if ($course->thumbnail_url)
                            <img
                                src="{{ $course->thumbnail_url }}"
                                alt="{{ $course->title }}"
                                class="w-full sm:w-44 h-32 rounded-2xl object-cover shrink-0 border border-neutral-100 shadow-sm"
                                onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                            >
                            <div class="w-full sm:w-44 h-32 rounded-2xl bg-orange-100 text-lb-orange items-center justify-center shrink-0 text-3xl font-extrabold shadow-sm hidden">
                                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                            </div>
                        @else
                            <div class="w-full sm:w-44 h-32 rounded-2xl bg-orange-100 text-lb-orange flex items-center justify-center shrink-0 text-3xl font-extrabold shadow-sm">
                                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                            </div>
                        @endif

                        <div class="space-y-2 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-700">
                                    <i class="{{ $course->category->icon ?? 'fas fa-tag' }} text-[10px]" aria-hidden="true"></i>
                                    <span>{{ $course->category->name }}</span>
                                </span>

                                @if ($course->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i> Published
                                    </span>
                                @elseif ($course->status === 'review')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-clock text-[10px]" aria-hidden="true"></i> In Review
                                    </span>
                                @elseif ($course->status === 'draft')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-neutral-100 text-neutral-700 border border-neutral-200">
                                        <i class="fas fa-pencil-alt text-[10px]" aria-hidden="true"></i> Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        <i class="fas fa-archive text-[10px]" aria-hidden="true"></i> Archived
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight leading-tight">
                                {{ $course->title }}
                            </h1>
                            <p class="text-xs sm:text-sm text-neutral-500">
                                {{ $course->subtitle ?? 'No subtitle provided' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 pt-1">
                                <span><i class="fas fa-clock text-neutral-400 mr-1" aria-hidden="true"></i> {{ $course->duration_hours }} Hours</span>
                                <span>•</span>
                                <span><i class="fas fa-signal text-neutral-400 mr-1" aria-hidden="true"></i> {{ ucfirst($course->difficulty_level) }}</span>
                                <span>•</span>
                                <span><i class="fas fa-language text-neutral-400 mr-1" aria-hidden="true"></i> {{ $course->language }}</span>
                                <span>•</span>
                                <span><i class="fas fa-link text-neutral-400 mr-1" aria-hidden="true"></i> /{{ $course->slug }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Quick Actions -->
                    <div class="flex flex-wrap lg:flex-col items-stretch gap-2 shrink-0 w-full lg:w-56">
                        @can('update', $course)
                            <a
                                href="{{ route('management.courses.edit', $course) }}"
                                class="flex-1 lg:flex-initial w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-neutral-200/90 bg-white hover:bg-orange-50 hover:border-orange-200 text-xs font-semibold text-neutral-800 hover:text-lb-orange transition-all shadow-2xs group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-neutral-100 group-hover:bg-orange-100 flex items-center justify-center text-neutral-500 group-hover:text-lb-orange shrink-0 transition-colors">
                                        <i class="fas fa-pen-to-square text-[11px]" aria-hidden="true"></i>
                                    </span>
                                    <span class="truncate">Edit Details</span>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] text-neutral-300 group-hover:text-lb-orange group-hover:translate-x-0.5 transition-all"></i>
                            </a>

                            <a
                                href="{{ route('management.courses.assignments.index', $course) }}"
                                class="flex-1 lg:flex-initial w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-neutral-200/90 bg-white hover:bg-orange-50 hover:border-orange-200 text-xs font-semibold text-neutral-800 hover:text-lb-orange transition-all shadow-2xs group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-orange-100 text-lb-orange flex items-center justify-center shrink-0">
                                        <i class="fas fa-file-lines text-[11px]" aria-hidden="true"></i>
                                    </span>
                                    <span class="truncate">Assignments</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-lb-orange shrink-0">
                                    {{ $course->assignments()->count() }}
                                </span>
                            </a>

                            <a
                                href="{{ route('management.courses.quizzes.index', $course) }}"
                                class="flex-1 lg:flex-initial w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-neutral-200/90 bg-white hover:bg-purple-50 hover:border-purple-200 text-xs font-semibold text-neutral-800 hover:text-purple-700 transition-all shadow-2xs group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-circle-question text-[11px]" aria-hidden="true"></i>
                                    </span>
                                    <span class="truncate">Quizzes</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 shrink-0">
                                    {{ $course->quizzes()->count() }}
                                </span>
                            </a>

                            <a
                                href="{{ route('management.courses.batches.index', $course) }}"
                                class="flex-1 lg:flex-initial w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-neutral-200/90 bg-white hover:bg-amber-50 hover:border-amber-200 text-xs font-semibold text-neutral-800 hover:text-amber-700 transition-all shadow-2xs group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-calendar-days text-[11px]" aria-hidden="true"></i>
                                    </span>
                                    <span class="truncate">Batches</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">
                                    {{ $course->batches()->count() }}
                                </span>
                            </a>

                            <a
                                href="{{ route('management.courses.live-classes.index', $course) }}"
                                class="flex-1 lg:flex-initial w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-neutral-200/90 bg-white hover:bg-blue-50 hover:border-blue-200 text-xs font-semibold text-neutral-800 hover:text-blue-700 transition-all shadow-2xs group"
                            >
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-video text-[11px]" aria-hidden="true"></i>
                                    </span>
                                    <span class="truncate">Live Classes</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 shrink-0">
                                    {{ $course->liveClasses()->count() }}
                                </span>
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Instructors Bar -->
                <div class="mt-6 pt-6 border-t border-neutral-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Instructors:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach ($course->instructors as $inst)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-neutral-100 text-neutral-800 text-xs font-semibold">
                                    <i class="fas fa-chalkboard-teacher text-lb-orange text-[10px]" aria-hidden="true"></i>
                                    <span>{{ $inst->name }}</span>
                                    @if ($inst->pivot->is_primary)
                                        <span class="text-[9px] uppercase tracking-wider bg-orange-200 text-lb-black font-extrabold px-1.5 py-0.2 rounded-full">Lead</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-xs text-neutral-400">
                        Created by {{ $course->creator->name }} on {{ $course->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>

            <!-- Publication Lifecycle Action Bar -->
            <div class="bg-gradient-to-r from-neutral-900 to-neutral-800 rounded-3xl p-6 text-white shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold flex items-center gap-2">
                            <i class="fas fa-rocket text-lb-orange" aria-hidden="true"></i>
                            <span>Publication Lifecycle</span>
                        </h2>
                        <p class="text-xs text-neutral-300 mt-0.5">
                            Current Stage: <span class="font-bold text-white uppercase">{{ $course->status }}</span>
                            @if ($course->published_at)
                                · Published on {{ $course->published_at->format('M d, Y') }}
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Submit for Review (Draft -> Review) -->
                        @if ($course->isDraft())
                            <form method="POST" action="{{ route('management.courses.status', $course) }}">
                                @csrf
                                <input type="hidden" name="status" value="review">
                                <button type="submit" class="px-4 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-xs font-bold text-white transition cursor-pointer">
                                    <i class="fas fa-paper-plane mr-1" aria-hidden="true"></i> Submit for Review
                                </button>
                            </form>
                        @endif

                        <!-- Publish Directly (Admin only: Draft/Review -> Published) -->
                        @can('publish', $course)
                            @if (! $course->isPublished())
                                <form method="POST" action="{{ route('management.courses.status', $course) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="published">
                                    <button type="submit" class="px-4 py-2 rounded-full bg-lb-orange hover:bg-[#E65500] text-xs font-bold text-white transition cursor-pointer">
                                        <i class="fas fa-check mr-1" aria-hidden="true"></i> Approve & Publish
                                    </button>
                                </form>
                            @endif
                        @endcan

                        <!-- Return to Draft (Review -> Draft) -->
                        @if ($course->isUnderReview())
                            @can('update', $course)
                                <form method="POST" action="{{ route('management.courses.status', $course) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="draft">
                                    <button type="submit" class="px-4 py-2 rounded-full bg-neutral-700 hover:bg-neutral-600 text-xs font-semibold text-white transition cursor-pointer">
                                        Return to Draft
                                    </button>
                                </form>
                            @endcan
                        @endif

                        <!-- View Live Storefront -->
                        @if ($course->isPublished())
                            <a
                                href="{{ route('courses.show', $course) }}"
                                target="_blank"
                                class="px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition border border-white/20 inline-flex items-center gap-1.5"
                            >
                                <i class="fas fa-external-link-alt text-[10px]" aria-hidden="true"></i>
                                <span>View Live Storefront</span>
                            </a>
                        @endif

                        <!-- Archive (Published -> Archived) -->
                        @if ($course->isPublished())
                            @can('archive', $course)
                                <button
                                    type="button"
                                    data-item="{{ $course->title }}"
                                    data-action="{{ route('management.courses.status', $course) }}"
                                    onclick="confirmDialog({
                                        title: 'Archive Course?',
                                        itemName: this.dataset.item,
                                        message: 'Archiving will remove this course from public discovery and search while preserving all current student enrollments and records.',
                                        action: this.dataset.action,
                                        method: 'POST',
                                        fields: { status: 'archived' },
                                        confirmText: 'Archive Course',
                                        confirmVariant: 'warning',
                                        icon: 'fas fa-archive'
                                    })"
                                    class="px-4 py-2 rounded-full bg-neutral-700 hover:bg-rose-700 text-xs font-semibold text-white transition cursor-pointer"
                                >
                                    <i class="fas fa-archive mr-1" aria-hidden="true"></i> Archive Course
                                </button>
                            @endcan
                        @endif

                        <!-- Restore to Draft (Archived -> Draft, Admin only) -->
                        @if ($course->isArchived())
                            @can('publish', $course)
                                <form method="POST" action="{{ route('management.courses.status', $course) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="draft">
                                    <button type="submit" class="px-4 py-2 rounded-full bg-neutral-700 hover:bg-neutral-600 text-xs font-semibold text-white transition cursor-pointer">
                                        Restore to Draft
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

            <!-- Course Assessments & Campus Operations Console -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Assignments Console Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-lb-orange">
                                <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Coursework
                            </span>
                            <span class="text-xs font-extrabold text-neutral-800">
                                {{ $course->assignments()->count() }} {{ Str::plural('Assignment', $course->assignments()->count()) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-extrabold text-lb-black">Assignments &amp; Submissions</h3>
                        <p class="text-xs text-neutral-500 leading-relaxed">
                            Create project briefs, set deadlines, and review/grade submitted student files.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                        <a
                            href="{{ route('management.courses.assignments.create', $course) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 text-xs font-semibold transition"
                        >
                            <i class="fas fa-plus text-xs text-lb-orange" aria-hidden="true"></i>
                            <span>Create</span>
                        </a>

                        <a
                            href="{{ route('management.courses.assignments.index', $course) }}"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-lb-orange text-white hover:bg-orange-600 text-xs font-bold transition shadow-sm"
                        >
                            <span>Manage</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Quizzes Console Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                                <i class="fas fa-question-circle text-xs" aria-hidden="true"></i> Knowledge Checks
                            </span>
                            <span class="text-xs font-extrabold text-neutral-800">
                                {{ $course->quizzes()->count() }} {{ Str::plural('Quiz', $course->quizzes()->count()) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-extrabold text-lb-black">Quizzes &amp; Questions</h3>
                        <p class="text-xs text-neutral-500 leading-relaxed">
                            Single-choice and true/false tests with automatic scoring and attempt limits.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                        <a
                            href="{{ route('management.courses.quizzes.create', $course) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 text-xs font-semibold transition"
                        >
                            <i class="fas fa-plus text-xs text-purple-600" aria-hidden="true"></i>
                            <span>Create</span>
                        </a>

                        <a
                            href="{{ route('management.courses.quizzes.index', $course) }}"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-purple-600 text-white hover:bg-purple-700 text-xs font-bold transition shadow-sm"
                        >
                            <span>Manage</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Physical Batches & Attendance Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                <i class="fas fa-building text-xs" aria-hidden="true"></i> Campus Batches
                            </span>
                            <span class="text-xs font-extrabold text-neutral-800">
                                {{ $course->batches()->count() }} {{ Str::plural('Batch', $course->batches()->count()) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-extrabold text-lb-black">Batches &amp; Attendance</h3>
                        <p class="text-xs text-neutral-500 leading-relaxed">
                            Manage classroom intakes, weekly timetables, session rosters, and student attendance.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                        <a
                            href="{{ route('management.courses.batches.create', $course) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 text-xs font-semibold transition"
                        >
                            <i class="fas fa-plus text-xs text-amber-600" aria-hidden="true"></i>
                            <span>Create</span>
                        </a>

                        <a
                            href="{{ route('management.courses.batches.index', $course) }}"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-amber-600 text-white hover:bg-amber-700 text-xs font-bold transition shadow-sm"
                        >
                            <span>Manage</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <!-- Interactive Live Classes Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                <i class="fas fa-video text-xs" aria-hidden="true"></i> Virtual Classes
                            </span>
                            <span class="text-xs font-extrabold text-neutral-800">
                                {{ $course->liveClasses()->count() }} {{ Str::plural('Session', $course->liveClasses()->count()) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-extrabold text-lb-black">Live Video Classes</h3>
                        <p class="text-xs text-neutral-500 leading-relaxed">
                            Schedule real-time workshops, review code live, and conduct interactive Q&amp;A sessions.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex items-center justify-between gap-2">
                        <a
                            href="{{ route('management.courses.live-classes.create', $course) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 text-xs font-semibold transition"
                        >
                            <i class="fas fa-plus text-xs text-blue-600" aria-hidden="true"></i>
                            <span>Schedule</span>
                        </a>

                        <a
                            href="{{ route('management.courses.live-classes.index', $course) }}"
                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-xs font-bold transition shadow-sm"
                        >
                            <span>Manage</span>
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery Options & Pricing Panel -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-cubes text-lb-orange" aria-hidden="true"></i>
                            <span>Configured Delivery Options & Pricing</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Single course entity with independent multi-delivery pricing and batch allocations.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse ($course->deliveryOptions as $opt)
                        <div class="p-5 rounded-2xl border border-neutral-200 bg-neutral-50/50 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold uppercase {{ $opt->delivery_mode === 'online' ? 'bg-blue-100 text-blue-700' : ($opt->delivery_mode === 'physical' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700') }}">
                                        {{ $opt->delivery_mode }}
                                    </span>
                                    <span class="text-xs font-semibold text-emerald-600">Active</span>
                                </div>
                                <h3 class="text-sm font-bold text-neutral-900">{{ $opt->title }}</h3>
                                <p class="text-xs text-neutral-500 mt-1">{{ $opt->description }}</p>

                                <div class="mt-4 pt-3 border-t border-neutral-200/60 text-xs space-y-1.5 text-neutral-600">
                                    @if ($opt->access_period_days)
                                        <p><i class="fas fa-calendar-alt text-neutral-400 mr-1.5"></i> Access: {{ $opt->access_period_days }} Days</p>
                                    @else
                                        <p><i class="fas fa-infinity text-neutral-400 mr-1.5"></i> Lifetime Access</p>
                                    @endif

                                    @if ($opt->max_capacity)
                                        <p><i class="fas fa-users text-neutral-400 mr-1.5"></i> Max Batch Capacity: {{ $opt->max_capacity }} Students</p>
                                    @endif

                                    <p><i class="fas fa-certificate text-neutral-400 mr-1.5"></i> Includes Verified Certificate</p>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-neutral-200/60 flex items-center justify-between">
                                <span class="text-xs text-neutral-500">Tuition Fee:</span>
                                <span class="text-base font-extrabold text-neutral-900">{{ $opt->formattedPrice() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 p-6 text-center text-xs text-neutral-400">
                            No delivery options configured. <a href="{{ route('management.courses.edit', $course) }}" class="text-lb-orange font-semibold underline">Edit course to add options</a>.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Curriculum Builder (Sections & Lessons) -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6" x-data>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-list-ol text-lb-orange" aria-hidden="true"></i>
                            <span>Course Curriculum & Modules</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Organize modules, lessons, video durations, and preview lessons.</p>
                    </div>

                    @can('update', $course)
                        <button
                            type="button"
                            @click="$dispatch('open-modal', 'add-section-modal')"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-section-modal' }))"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold transition cursor-pointer"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Add Section Module</span>
                        </button>
                    @endcan
                </div>

                <!-- Sections List -->
                @if ($course->sections->isEmpty())
                    <div class="p-8 text-center bg-neutral-50 rounded-2xl border border-neutral-100">
                        <p class="text-xs text-neutral-500 mb-3">No curriculum sections have been authored yet.</p>
                        @can('update', $course)
                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'add-section-modal')"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-section-modal' }))"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] transition cursor-pointer"
                            >
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                                <span>Create First Module</span>
                            </button>
                        @endcan
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($course->sections as $index => $section)
                            <div class="border border-neutral-200 rounded-2xl overflow-hidden" x-data="{ open: true }">
                                <!-- Section Header -->
                                <div class="bg-neutral-50 px-5 py-3.5 flex items-center justify-between cursor-pointer select-none" @click="open = !open">
                                    <div class="flex items-center gap-3">
                                        <i class="fas" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="text-xs text-neutral-400"></i>
                                        <div>
                                            <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Module {{ $index + 1 }}:</span>
                                            <h3 class="text-sm font-bold text-neutral-900 inline ml-1">{{ $section->clean_title }}</h3>
                                            @if ($section->description)
                                                <p class="text-[11px] text-neutral-500 mt-0.5">{{ $section->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3" @click.stop>
                                        <span class="text-xs text-neutral-400">{{ $section->lessons->count() }} Lessons</span>

                                        @can('update', $course)
                                            <button
                                                type="button"
                                                @click="$dispatch('open-modal', 'add-lesson-modal-{{ $section->id }}')"
                                                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-lesson-modal-{{ $section->id }}' }))"
                                                class="px-3 py-1 rounded-full bg-white border border-neutral-200 hover:border-neutral-300 text-xs font-semibold text-neutral-700 transition cursor-pointer"
                                            >
                                                <i class="fas fa-plus text-[10px] text-lb-orange mr-1"></i> Add Lesson
                                            </button>

                                            <button
                                                type="button"
                                                data-item="{{ $section->title }}"
                                                data-action="{{ route('management.sections.destroy', $section) }}"
                                                onclick="confirmDialog({
                                                    title: 'Delete Module Section',
                                                    itemName: this.dataset.item,
                                                    message: 'Are you sure you want to delete this module section? All lessons inside this section will be permanently deleted.',
                                                    action: this.dataset.action,
                                                    method: 'DELETE',
                                                    confirmText: 'Delete Section',
                                                    confirmVariant: 'danger'
                                                })"
                                                class="p-1.5 text-neutral-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                                title="Delete Section"
                                            >
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                <!-- Lessons List -->
                                <div x-show="open" class="divide-y divide-neutral-100 p-2 sm:p-3 bg-white">
                                    @forelse ($section->lessons as $lesson)
                                        <div class="px-3 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-neutral-50/60 rounded-xl transition">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <i class="{{ $lesson->type === 'video' ? 'fas fa-play-circle text-blue-500' : ($lesson->type === 'article' ? 'fas fa-file-alt text-neutral-500' : 'fas fa-tasks text-purple-500') }} text-sm shrink-0" aria-hidden="true"></i>
                                                <div class="min-w-0 flex items-center gap-2 flex-wrap">
                                                    <span class="text-xs font-semibold text-neutral-800 truncate">{{ $lesson->title }}</span>
                                                    @if ($lesson->is_preview)
                                                        <span class="px-2 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold shrink-0">Preview</span>
                                                    @endif

                                                    @if ($lesson->type === 'video')
                                                        @if ($lesson->video_status === 'ready')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                                <i class="fas fa-check-circle text-[9px]"></i> Ready
                                                            </span>
                                                        @elseif ($lesson->video_status === 'processing')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                                                <i class="fas fa-spinner fa-spin text-[9px]"></i> Encoding (Mux)
                                                            </span>
                                                        @elseif ($lesson->video_status === 'waiting_upload')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                                                                <i class="fas fa-cloud-upload-alt text-[9px]"></i> Waiting Upload
                                                            </span>
                                                        @elseif ($lesson->video_status === 'errored')
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0" title="{{ $lesson->video_error_message }}">
                                                                <i class="fas fa-exclamation-triangle text-[9px]"></i> Failed
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 text-neutral-500 border border-neutral-200 shrink-0">
                                                                <i class="fas fa-video-slash text-[9px]"></i> No Video
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 sm:gap-3 shrink-0 self-end sm:self-auto">
                                                <span class="text-xs text-neutral-400 font-mono">{{ $lesson->duration_minutes }} min</span>

                                                @if ($lesson->type === 'video')
                                                    @can('update', $course)
                                                        @if (in_array($lesson->video_status, ['processing', 'waiting_upload']))
                                                            <button
                                                                type="button"
                                                                onclick="syncVideoStatus(this, '{{ route('management.lessons.video-sync', [$course, $lesson]) }}')"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-neutral-100 hover:bg-neutral-200 text-neutral-700 transition cursor-pointer"
                                                                title="Check encoding status from Mux"
                                                            >
                                                                <i class="fas fa-sync-alt text-[10px]"></i>
                                                                <span>Sync</span>
                                                            </button>
                                                        @endif

                                                        <button
                                                            type="button"
                                                            data-lesson-id="{{ $lesson->id }}"
                                                            data-lesson-title="{{ $lesson->title }}"
                                                            data-upload-url="{{ route('management.lessons.video-upload', [$course, $lesson]) }}"
                                                            data-sync-url="{{ route('management.lessons.video-sync', [$course, $lesson]) }}"
                                                            onclick="openVideoUploader(this.dataset.lessonId, this.dataset.lessonTitle, this.dataset.uploadUrl, this.dataset.syncUrl)"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition shadow-xs cursor-pointer {{ $lesson->video_status === 'ready' ? 'bg-neutral-100 hover:bg-neutral-200 text-neutral-700 border border-neutral-200' : 'bg-lb-orange hover:bg-[#E65500] text-white' }}"
                                                            title="{{ $lesson->video_status === 'ready' ? 'Replace Video on Mux' : 'Upload Video to Mux' }}"
                                                        >
                                                            <i class="fas fa-cloud-upload-alt text-xs"></i>
                                                            <span>{{ $lesson->video_status === 'ready' ? 'Replace' : 'Upload Video' }}</span>
                                                        </button>
                                                    @endcan

                                                    @if ($lesson->video_status === 'ready')
                                                        <a
                                                            href="{{ route('learning.show', [$course, $lesson]) }}"
                                                            target="_blank"
                                                            class="p-1.5 text-neutral-400 hover:text-lb-orange rounded-lg transition"
                                                            title="Watch in Student Player"
                                                        >
                                                            <i class="fas fa-play text-xs"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                                @can('update', $course)
                                                    <button
                                                        type="button"
                                                        data-item="{{ $lesson->title }}"
                                                        data-action="{{ route('management.lessons.destroy', $lesson) }}"
                                                        onclick="confirmDialog({
                                                            title: 'Delete Lesson',
                                                            itemName: this.dataset.item,
                                                            message: 'Are you sure you want to permanently delete this lesson? Student progress and materials associated with this lesson will be removed.',
                                                            action: this.dataset.action,
                                                            method: 'DELETE',
                                                            confirmText: 'Delete Lesson',
                                                            confirmVariant: 'danger'
                                                        })"
                                                        class="text-neutral-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition cursor-pointer"
                                                        title="Delete Lesson"
                                                    >
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-xs text-neutral-400">
                                            No lessons yet in this module. Click "Add Lesson" above.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Modal: Add Lesson to this section -->
                            <x-modal name="add-lesson-modal-{{ $section->id }}" title="Add Lesson to Module: {{ $section->title }}" max-width="md">
                                <form method="POST" action="{{ route('management.sections.lessons.store', $section) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-neutral-700 mb-1">Lesson Title *</label>
                                        <input type="text" name="title" required placeholder="e.g. Introduction to Routing & Controllers" class="w-full px-3 py-2 rounded-xl border border-neutral-300 text-xs focus:ring-2 focus:ring-lb-orange">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-neutral-700 mb-1">Type *</label>
                                            <select name="type" class="w-full px-3 py-2 rounded-xl border border-neutral-300 text-xs">
                                                <option value="video">Video</option>
                                                <option value="article">Article / Reading</option>
                                                <option value="live_class">Live Class</option>
                                                <option value="quiz">Quiz</option>
                                                <option value="assignment">Assignment</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-neutral-700 mb-1">Duration (Minutes) *</label>
                                            <input type="number" min="0" name="duration_minutes" value="15" required class="w-full px-3 py-2 rounded-xl border border-neutral-300 text-xs">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="is_preview" id="preview_{{ $section->id }}" value="1" class="w-4 h-4 rounded text-lb-orange focus:ring-lb-orange">
                                        <label for="preview_{{ $section->id }}" class="text-xs text-neutral-700 cursor-pointer">Allow free preview before enrollment</label>
                                    </div>
                                    <div class="pt-3 flex justify-end gap-2">
                                        <button type="submit" class="px-5 py-2.5 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] cursor-pointer">
                                            Save Lesson
                                        </button>
                                    </div>
                                </form>
                            </x-modal>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Modal: Add Section -->
            <x-modal name="add-section-modal" title="Add Curriculum Module / Section" max-width="md">
                <form method="POST" action="{{ route('management.courses.sections.store', $course) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-neutral-700 mb-1">Module Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Module 1: Architecture & Foundations" class="w-full px-3 py-2 rounded-xl border border-neutral-300 text-xs focus:ring-2 focus:ring-lb-orange">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-neutral-700 mb-1">Brief Description</label>
                        <textarea name="description" rows="2" placeholder="Overview of concepts covered in this module..." class="w-full px-3 py-2 rounded-xl border border-neutral-300 text-xs"></textarea>
                    </div>
                    <div class="pt-3 flex justify-end gap-2">
                        <button type="submit" class="px-5 py-2.5 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] cursor-pointer">
                            Create Module
                        </button>
                    </div>
                </form>
            </x-modal>

            <!-- Modern Mux Direct Video Uploader Modal -->
            <div
                x-data="{
                    isOpen: false,
                    lessonId: null,
                    lessonTitle: '',
                    uploadSessionUrl: '',
                    syncUrl: '',
                    selectedFile: null,
                    fileName: '',
                    fileSizeFormatted: '',
                    uploading: false,
                    progress: 0,
                    statusText: '',
                    errorMessage: '',
                    isComplete: false,
                    isEncoding: false,
                    pollTimer: null,

                    openModal(detail) {
                        this.lessonId = detail.lessonId;
                        this.lessonTitle = detail.lessonTitle;
                        this.uploadSessionUrl = detail.uploadSessionUrl;
                        this.syncUrl = detail.syncUrl;
                        this.selectedFile = null;
                        this.fileName = '';
                        this.fileSizeFormatted = '';
                        this.uploading = false;
                        this.progress = 0;
                        this.statusText = '';
                        this.errorMessage = '';
                        this.isComplete = false;
                        this.isEncoding = false;
                        if (this.pollTimer) clearInterval(this.pollTimer);
                        if (this.$refs.videoFileInput) this.$refs.videoFileInput.value = '';
                        this.isOpen = true;
                    },

                    close() {
                        if (this.uploading) {
                            if (!confirm('Upload is in progress. Are you sure you want to cancel?')) return;
                        }
                        if (this.pollTimer) clearInterval(this.pollTimer);
                        this.isOpen = false;
                        if (this.isComplete) {
                            window.location.reload();
                        }
                    },

                    onFilePicked(e) {
                        const files = e.target.files;
                        if (!files || files.length === 0) return;
                        this.selectedFile = files[0];
                        this.fileName = this.selectedFile.name;
                        const bytes = this.selectedFile.size;
                        if (bytes < 1024 * 1024) {
                            this.fileSizeFormatted = (bytes / 1024).toFixed(1) + ' KB';
                        } else if (bytes < 1024 * 1024 * 1024) {
                            this.fileSizeFormatted = (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                        } else {
                            this.fileSizeFormatted = (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
                        }
                        this.errorMessage = '';
                    },

                    async startUpload() {
                        if (!this.selectedFile) {
                            this.errorMessage = 'Please choose a video file first.';
                            return;
                        }

                        this.uploading = true;
                        this.progress = 0;
                        this.errorMessage = '';
                        this.statusText = 'Requesting secure direct-upload session from Mux...';

                        const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';

                        try {
                            // 1. Request direct upload URL from LearnBuddy backend
                            const sessionRes = await fetch(this.uploadSessionUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });

                            const sessionData = await sessionRes.json();

                            if (!sessionRes.ok || !sessionData.success || !sessionData.upload_url) {
                                throw new Error(sessionData.message || 'Could not initiate Mux direct upload session.');
                            }

                            // 2. Direct browser-to-Mux upload via HTTP PUT
                            this.statusText = 'Uploading directly to Mux Cloud: 0%';

                            const xhr = new XMLHttpRequest();
                            xhr.open('PUT', sessionData.upload_url);

                            xhr.upload.onprogress = (e) => {
                                if (e.lengthComputable) {
                                    this.progress = Math.round((e.loaded / e.total) * 100);
                                    const uploadedMB = (e.loaded / (1024 * 1024)).toFixed(1);
                                    const totalMB = (e.total / (1024 * 1024)).toFixed(1);
                                    this.statusText = `Uploading to Mux: ${this.progress}% (${uploadedMB} MB / ${totalMB} MB)`;
                                }
                            };

                            xhr.onload = () => {
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    this.uploading = false;
                                    this.isComplete = true;
                                    this.isEncoding = true;
                                    this.progress = 100;
                                    this.statusText = 'Upload complete! Mux is now encoding and generating signed HLS streaming chunks.';
                                    this.startPolling();
                                } else {
                                    this.uploading = false;
                                    this.errorMessage = 'Mux upload failed with HTTP status: ' + xhr.status;
                                }
                            };

                            xhr.onerror = () => {
                                this.uploading = false;
                                this.errorMessage = 'Network connection lost during upload to Mux. Please check your connection and retry.';
                            };

                            xhr.send(this.selectedFile);

                        } catch (err) {
                            this.uploading = false;
                            this.errorMessage = err.message || 'An unexpected error occurred during upload.';
                        }
                    },

                    startPolling() {
                        let attempts = 0;
                        const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';

                        this.pollTimer = setInterval(async () => {
                            attempts++;
                            try {
                                const res = await fetch(this.syncUrl, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if (data.status === 'ready') {
                                    clearInterval(this.pollTimer);
                                    this.isEncoding = false;
                                    this.statusText = 'Video is encoded and ready! Lesson duration automatically updated (' + (data.duration_minutes || 0) + ' min).';
                                } else if (data.status === 'errored') {
                                    clearInterval(this.pollTimer);
                                    this.isEncoding = false;
                                    this.errorMessage = data.message || 'Mux encoding failed for this file.';
                                } else if (attempts >= 15) {
                                    clearInterval(this.pollTimer);
                                    this.statusText = 'Upload saved on Mux. Encoding is taking a little longer in the background. You can close this and click Sync anytime.';
                                }
                            } catch (e) {
                                console.error('Polling error:', e);
                            }
                        }, 4000);
                    }
                }"
                x-on:open-video-uploader.window="openModal($event.detail)"
                x-on:keydown.escape.window="close()"
                x-show="isOpen"
                x-cloak
                class="fixed inset-0 z-[100] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
                role="dialog"
                aria-modal="true"
            >
                <!-- Backdrop -->
                <div
                    x-show="isOpen"
                    x-transition:enter="ease-out duration-250"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
                    @click="close()"
                ></div>

                <!-- Modal Window -->
                <div
                    x-show="isOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-3xl shadow-2xl transform transition-all w-full max-w-lg p-6 sm:p-7 z-10 border border-neutral-100 overflow-hidden"
                    @click.stop
                >
                    <!-- Close button -->
                    <button
                        type="button"
                        @click="close()"
                        class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-700 p-2 rounded-xl transition cursor-pointer"
                        aria-label="Close dialog"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>

                    <!-- Header -->
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 text-lb-orange flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="flex-1 pr-6 min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-neutral-900">
                                Upload Lesson Video (Mux)
                            </h3>
                            <p class="text-xs text-neutral-500 truncate mt-0.5" x-text="lessonTitle"></p>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="errorMessage" class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5" x-cloak>
                        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0"></i>
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- File Dropzone -->
                    <div x-show="!isComplete && !uploading" class="space-y-4">
                        <label
                            class="border-2 border-dashed border-neutral-200 hover:border-lb-orange rounded-3xl p-6 flex flex-col items-center justify-center gap-2 cursor-pointer bg-neutral-50/50 hover:bg-orange-50/30 transition text-center"
                        >
                            <input
                                type="file"
                                x-ref="videoFileInput"
                                @change="onFilePicked($event)"
                                accept="video/mp4,video/quicktime,video/webm,video/x-m4v,video/*"
                                class="hidden"
                            >
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-xs text-neutral-400 flex items-center justify-center text-xl">
                                <i class="fas fa-cloud-upload-alt text-lb-orange"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-neutral-800">
                                    Click to select video file
                                </p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">
                                    MP4, MOV, WEBM or M4V formats supported
                                </p>
                            </div>
                        </label>

                        <!-- Selected File Info -->
                        <div x-show="fileName" class="p-3.5 rounded-2xl bg-neutral-100/80 border border-neutral-200 flex items-center justify-between gap-3" x-cloak>
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i class="fas fa-file-video text-lb-orange text-base shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-neutral-800 truncate" x-text="fileName"></p>
                                    <p class="text-[10px] text-neutral-500" x-text="fileSizeFormatted"></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-emerald-700 border border-neutral-200 shrink-0">
                                Selected
                            </span>
                        </div>
                    </div>

                    <!-- Upload Progress Area -->
                    <div x-show="uploading || isComplete" class="space-y-4 my-2" x-cloak>
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-800">
                            <span class="flex items-center gap-1.5">
                                <i x-show="uploading" class="fas fa-arrow-up text-lb-orange animate-bounce"></i>
                                <i x-show="isEncoding" class="fas fa-cog fa-spin text-amber-500"></i>
                                <i x-show="!uploading && !isEncoding" class="fas fa-check-circle text-emerald-600"></i>
                                <span x-text="uploading ? 'Uploading Video' : (isEncoding ? 'Encoding on Mux Cloud' : 'Ready to Stream')"></span>
                            </span>
                            <span class="font-mono text-lb-orange" x-text="progress + '%'"></span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-neutral-100 rounded-full h-3 overflow-hidden p-0.5 border border-neutral-200">
                            <div
                                class="bg-gradient-to-r from-orange-500 to-lb-orange h-full rounded-full transition-all duration-200 shadow-sm"
                                :style="'width: ' + progress + '%'"
                            ></div>
                        </div>

                        <p class="text-xs text-neutral-600 leading-relaxed" x-text="statusText"></p>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-6 pt-5 border-t border-neutral-100 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="close()"
                            :disabled="uploading"
                            class="px-5 py-2.5 rounded-full text-xs font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition cursor-pointer disabled:opacity-50"
                        >
                            <span x-text="isComplete ? 'Done & Close' : 'Cancel'"></span>
                        </button>

                        <button
                            x-show="!isComplete"
                            type="button"
                            @click="startUpload()"
                            :disabled="uploading || !selectedFile"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-bold text-white bg-lb-orange hover:bg-[#E65500] transition shadow-sm cursor-pointer disabled:opacity-50"
                        >
                            <i x-show="uploading" class="fas fa-spinner fa-spin text-xs"></i>
                            <i x-show="!uploading" class="fas fa-cloud-upload-alt text-xs"></i>
                            <span x-text="uploading ? 'Uploading...' : 'Start Upload to Mux'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <script>
                window.openVideoUploader = function(lessonId, lessonTitle, uploadSessionUrl, syncUrl) {
                    window.dispatchEvent(new CustomEvent('open-video-uploader', {
                        detail: { lessonId, lessonTitle, uploadSessionUrl, syncUrl }
                    }));
                };

                window.syncVideoStatus = async function(btn, syncUrl) {
                    const icon = btn.querySelector('i');
                    if (icon) icon.classList.add('fa-spin');
                    const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                    try {
                        const res = await fetch(syncUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.status === 'ready') {
                            window.location.reload();
                        } else {
                            alert('Mux Status: ' + (data.status || 'unknown') + '\n' + (data.message || ''));
                        }
                    } catch (e) {
                        alert('Could not sync status: ' + e.message);
                    } finally {
                        if (icon) icon.classList.remove('fa-spin');
                    }
                };
            </script>
        </div>
    </section>
</x-layouts.public>
