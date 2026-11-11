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
                            <img src="{{ $course->thumbnail_url }}" alt="" class="w-full sm:w-44 h-32 rounded-2xl object-cover shrink-0 border border-neutral-100 shadow-sm">
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
                    <div class="flex flex-wrap lg:flex-col items-end gap-2 shrink-0">
                        @can('update', $course)
                            <a
                                href="{{ route('management.courses.edit', $course) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition"
                            >
                                <i class="fas fa-edit text-xs" aria-hidden="true"></i>
                                <span>Edit Details</span>
                            </a>
                            <a
                                href="{{ route('management.courses.assignments.index', $course) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition"
                            >
                                <i class="fas fa-file-alt text-xs text-lb-orange" aria-hidden="true"></i>
                                <span>Assignments ({{ $course->assignments()->count() }})</span>
                            </a>
                            <a
                                href="{{ route('management.courses.quizzes.index', $course) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition"
                            >
                                <i class="fas fa-question-circle text-xs text-purple-600" aria-hidden="true"></i>
                                <span>Quizzes ({{ $course->quizzes()->count() }})</span>
                            </a>
                            <a
                                href="{{ route('management.courses.batches.index', $course) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition"
                            >
                                <i class="fas fa-calendar-alt text-xs text-amber-600" aria-hidden="true"></i>
                                <span>Batches ({{ $course->batches()->count() }})</span>
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

                        <!-- Archive (Published -> Archived) -->
                        @if ($course->isPublished())
                            @can('archive', $course)
                                <form method="POST" action="{{ route('management.courses.status', $course) }}" onsubmit="return confirm('Archive this course from active discovery?');">
                                    @csrf
                                    <input type="hidden" name="status" value="archived">
                                    <button type="submit" class="px-4 py-2 rounded-full bg-neutral-700 hover:bg-rose-700 text-xs font-semibold text-white transition cursor-pointer">
                                        <i class="fas fa-archive mr-1" aria-hidden="true"></i> Archive Course
                                    </button>
                                </form>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                            <h3 class="text-sm font-bold text-neutral-900 inline ml-1">{{ $section->title }}</h3>
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

                                            <form method="POST" action="{{ route('management.sections.destroy', $section) }}" onsubmit="return confirm('Delete this entire section and its lessons?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-neutral-400 hover:text-rose-600 transition cursor-pointer" title="Delete Section">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>

                                <!-- Lessons List -->
                                <div x-show="open" class="divide-y divide-neutral-100 p-2 sm:p-3 bg-white">
                                    @forelse ($section->lessons as $lesson)
                                        <div class="px-3 py-2.5 flex items-center justify-between hover:bg-neutral-50/60 rounded-xl transition">
                                            <div class="flex items-center gap-3">
                                                <i class="{{ $lesson->type === 'video' ? 'fas fa-play-circle text-blue-500' : ($lesson->type === 'article' ? 'fas fa-file-alt text-neutral-500' : 'fas fa-tasks text-purple-500') }} text-sm" aria-hidden="true"></i>
                                                <span class="text-xs font-medium text-neutral-800">{{ $lesson->title }}</span>
                                                @if ($lesson->is_preview)
                                                    <span class="px-2 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Preview</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <span class="text-xs text-neutral-400 font-mono">{{ $lesson->duration_minutes }} min</span>

                                                @can('update', $course)
                                                    <form method="POST" action="{{ route('management.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-neutral-300 hover:text-rose-500 p-1 cursor-pointer" title="Delete Lesson">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </form>
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
        </div>
    </section>
</x-layouts.public>
