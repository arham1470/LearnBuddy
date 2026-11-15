<x-layouts.public :title="$course->title . ' · LearnBuddy'">
    @php
        $primaryInstructor = $course->primaryInstructor();
        $rating = $course->averageRating();
        $reviewsCount = $course->reviewsCount();
        $duration = $course->formattedDuration();
        $deliveryModes = $course->deliveryModes();
        $hasPhysical = in_array('physical', $deliveryModes, true);
        $hasOnline = in_array('online', $deliveryModes, true);
        $isHybrid = in_array('hybrid', $deliveryModes, true) || ($hasPhysical && $hasOnline);
        $defaultOption = $course->deliveryOptions->first();
    @endphp

    <!-- HERO SECTION -->
    <section class="bg-neutral-900 text-white pt-8 sm:pt-12 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] relative overflow-hidden">
        <!-- Subtle background glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-lb-orange/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1140px] mx-auto px-4 sm:px-6 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center flex-wrap gap-2 text-xs text-neutral-400 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span class="text-neutral-600">/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-white transition">Courses</a>
                @if ($course->category)
                    <span class="text-neutral-600">/</span>
                    <a href="{{ route('courses.index', ['category' => $course->category->slug]) }}" class="hover:text-white transition">{{ $course->category->name }}</a>
                @endif
                <span class="text-neutral-600">/</span>
                <span class="text-neutral-300 truncate max-w-xs sm:max-w-md">{{ $course->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Course Hero Information -->
                <div class="lg:col-span-8 space-y-4">
                    <!-- Badges Row -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($course->category)
                            <a href="{{ route('courses.index', ['category' => $course->category->slug]) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 hover:bg-white/20 text-white transition">
                                <i class="{{ $course->category->icon ?? 'fas fa-folder' }} text-xs text-lb-orange" aria-hidden="true"></i>
                                <span>{{ $course->category->name }}</span>
                            </a>
                        @endif

                        @if ($isHybrid)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-neutral-800 text-white border border-neutral-700 shadow-sm">
                                <i class="fas fa-layer-group text-lb-orange text-[10px]" aria-hidden="true"></i>
                                <span>Online + Physical Campus</span>
                            </span>
                        @elseif ($hasPhysical)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-amber-500/20 text-amber-300 border border-amber-500/30 shadow-sm">
                                <i class="fas fa-chalkboard-teacher text-[10px]" aria-hidden="true"></i>
                                <span>Physical Classroom Batch</span>
                            </span>
                        @elseif ($hasOnline)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-lb-orange/20 text-orange-300 border border-lb-orange/30 shadow-sm">
                                <i class="fas fa-play-circle text-[10px]" aria-hidden="true"></i>
                                <span>Online Self-Paced</span>
                            </span>
                        @endif

                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Verified Curriculum
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                        {{ $course->title }}
                    </h1>

                    <!-- Subtitle -->
                    @if ($course->subtitle)
                        <p class="text-sm sm:text-base text-neutral-300 leading-relaxed max-w-3xl">
                            {{ $course->subtitle }}
                        </p>
                    @endif

                    <!-- Instructor & Meta Row -->
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-2 text-xs sm:text-sm text-neutral-300">
                        @if ($primaryInstructor)
                            <a href="{{ route('instructors.show', $primaryInstructor) }}" class="flex items-center gap-2 hover:text-white transition group">
                                <div class="w-8 h-8 rounded-full bg-lb-peach text-lb-orange flex items-center justify-center font-bold text-xs shrink-0 border border-white/20">
                                    {{ strtoupper(substr($primaryInstructor->name, 0, 2)) }}
                                </div>
                                <span>Created by <strong class="text-white group-hover:underline">{{ $primaryInstructor->name }}</strong></span>
                            </a>
                        @endif

                        <!-- Rating Badge (real data only) -->
                        @if ($rating && $reviewsCount > 0)
                            <div class="flex items-center gap-1.5">
                                <span class="flex text-amber-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= round($rating) ? 'text-amber-400' : 'text-neutral-600' }}"></i>
                                    @endfor
                                </span>
                                <span class="font-bold text-white">{{ number_format($rating, 1) }}</span>
                                <span class="text-neutral-400">({{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }})</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1 text-neutral-400">
                                <i class="far fa-star text-neutral-500"></i>
                                <span>Newly published (No reviews yet)</span>
                            </div>
                        @endif
                    </div>

                    <!-- Course Meta Chips -->
                    <div class="flex flex-wrap items-center gap-3 pt-3 text-xs text-neutral-400 border-t border-neutral-800">
                        @if ($duration)
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-clock text-lb-orange" aria-hidden="true"></i>
                                <span>{{ $duration }} on-demand</span>
                            </div>
                        @endif

                        @if ($course->language)
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-globe text-neutral-500" aria-hidden="true"></i>
                                <span>Language: <strong class="text-neutral-200">{{ $course->language }}</strong></span>
                            </div>
                        @endif

                        @if ($course->difficulty_level)
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-signal text-neutral-500" aria-hidden="true"></i>
                                <span>Level: <strong class="text-neutral-200 capitalize">{{ str_replace('_', ' ', $course->difficulty_level) }}</strong></span>
                            </div>
                        @endif

                        @if ($course->published_at)
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-calendar-check text-neutral-500" aria-hidden="true"></i>
                                <span>Published: {{ $course->published_at->format('M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Course Preview Box (Mobile View Only) -->
                <div class="lg:hidden w-full">
                    <div class="bg-white rounded-3xl p-6 text-neutral-900 shadow-2xl border border-neutral-200">
                        <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider block mb-1">Tuition Pricing</span>
                        <div class="text-2xl font-extrabold text-lb-orange mb-4">
                            {{ $course->formattedPriceRange() }}
                        </div>

                        <div class="space-y-2">
                            @auth
                                @if (auth()->user()->isEnrolledIn($course) && auth()->user()->getEnrollmentFor($course)?->status === \App\Models\Enrollment::STATUS_ACTIVE)
                                    <x-button as="a" href="{{ route('learning.index', $course) }}" variant="primary" class="w-full text-center">
                                        <i class="fas fa-play text-xs mr-1"></i> Start Learning
                                    </x-button>
                                @else
                                    <x-button as="a" href="#delivery-options" variant="primary" class="w-full text-center">
                                        View Enrollment Options
                                    </x-button>
                                @endif
                            @else
                                <x-button as="a" href="{{ route('register') }}" variant="primary" class="w-full text-center">
                                    Create Account to Enroll
                                </x-button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN BODY CONTENT + STICKY SIDEBAR -->
    <section class="py-12 sm:py-16 bg-lb-off-white" x-data="{ selectedOptionId: {{ $defaultOption?->id ?? 'null' }} }">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- LEFT CONTENT AREA (8 Cols) -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- What You'll Learn Box -->
                    @if (!empty($course->learning_outcomes) && is_array($course->learning_outcomes))
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                            <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500" aria-hidden="true"></i>
                                <span>What you will learn</span>
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                                @foreach ($course->learning_outcomes as $outcome)
                                    <div class="flex items-start gap-2.5 text-xs sm:text-sm text-neutral-700">
                                        <i class="fas fa-check text-emerald-500 mt-1 shrink-0 text-xs" aria-hidden="true"></i>
                                        <span class="leading-relaxed">{{ $outcome }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Course Description & Overview -->
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                        <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-file-alt text-lb-orange" aria-hidden="true"></i>
                            <span>Course Overview</span>
                        </h2>

                        <div class="prose prose-sm max-w-none text-neutral-600 text-xs sm:text-sm leading-relaxed space-y-3">
                            <p class="whitespace-pre-line">{{ $course->description }}</p>
                        </div>

                        @if (!empty($course->prerequisites) && is_array($course->prerequisites))
                            <div class="pt-6 border-t border-neutral-100">
                                <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider mb-3">Prerequisites</h3>
                                <ul class="space-y-2 text-xs sm:text-sm text-neutral-600">
                                    @foreach ($course->prerequisites as $prereq)
                                        <li class="flex items-center gap-2">
                                            <i class="fas fa-circle text-[6px] text-lb-orange" aria-hidden="true"></i>
                                            <span>{{ $prereq }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Configured Delivery Options Details -->
                    <div id="delivery-options" class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-6">
                        <div class="border-b border-neutral-100 pb-4">
                            <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-cubes text-lb-orange" aria-hidden="true"></i>
                                <span>Configured Delivery Modes &amp; Pricing</span>
                            </h2>
                            <p class="text-xs text-neutral-500 mt-1">
                                Choose how you want to experience this course. Each delivery option includes independent pricing and batch allocations.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($course->deliveryOptions as $opt)
                                <div
                                    class="p-5 rounded-2xl border-2 transition cursor-pointer flex flex-col justify-between space-y-4"
                                    :class="selectedOptionId === {{ $opt->id }} ? 'border-lb-orange bg-orange-50/20' : 'border-neutral-200 bg-neutral-50/50 hover:border-neutral-300'"
                                    @click="selectedOptionId = {{ $opt->id }}"
                                >
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $opt->delivery_mode === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $opt->delivery_mode === 'online' ? 'Online Stream' : 'Physical Classroom' }}
                                            </span>
                                            <span class="text-xs font-bold" :class="selectedOptionId === {{ $opt->id }} ? 'text-lb-orange' : 'text-neutral-400'">
                                                <i class="fas" :class="selectedOptionId === {{ $opt->id }} ? 'fa-check-circle' : 'fa-circle'"></i>
                                            </span>
                                        </div>

                                        <h3 class="text-sm font-bold text-neutral-900 mb-1">{{ $opt->title }}</h3>
                                        <p class="text-xs text-neutral-500 leading-relaxed">{{ $opt->description }}</p>

                                        <ul class="mt-4 pt-3 border-t border-neutral-200/60 text-xs space-y-1.5 text-neutral-600">
                                            @if ($opt->includes_recorded_lessons)
                                                <li class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-500 text-[10px]"></i> Recorded video lectures</li>
                                            @endif
                                            @if ($opt->includes_physical_sessions)
                                                <li class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-500 text-[10px]"></i> Guided weekend physical laboratory sessions</li>
                                            @endif
                                            @if ($opt->includes_certificate)
                                                <li class="flex items-center gap-1.5"><i class="fas fa-check text-emerald-500 text-[10px]"></i> Verifiable completion certificate</li>
                                            @endif
                                            @if ($opt->max_capacity)
                                                <li class="flex items-center gap-1.5"><i class="fas fa-users text-neutral-400 text-[10px]"></i> Max capacity: {{ $opt->max_capacity }} seats</li>
                                            @endif
                                        </ul>
                                    </div>

                                    <div class="pt-3 border-t border-neutral-200/60">
                                        <a
                                            href="{{ route('enrollments.create', ['course' => $course, 'option_id' => $opt->id]) }}"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs sm:text-sm font-bold transition shadow-lb-sm hover:shadow-md cursor-pointer group"
                                        >
                                            <span>Select Option &bull; {{ $opt->formattedPrice() }}</span>
                                            <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Physical Batches & Locations (Displayed only when physical batches exist) -->
                    @php
                        $physicalBatches = $course->deliveryOptions->flatMap->batches;
                    @endphp
                    @if ($physicalBatches->isNotEmpty())
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-5">
                            <div class="border-b border-neutral-100 pb-4">
                                <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-lb-orange" aria-hidden="true"></i>
                                    <span>Sri Lankan Physical Campus Batches</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">
                                    In-person lectures, hardware equipment, and laboratory practicals conducted in accordance with Sri Lankan scheduling (Asia/Colombo).
                                </p>
                            </div>

                            <div class="space-y-4">
                                @foreach ($physicalBatches as $batch)
                                    <div class="p-4 sm:p-5 rounded-2xl border border-neutral-200 bg-neutral-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">
                                                    Batch: {{ $batch->batch_code }}
                                                </span>
                                                <span class="text-xs font-bold text-emerald-600">Open for Enrollment</span>
                                            </div>
                                            <h3 class="text-sm font-bold text-neutral-900">{{ $batch->batch_name }}</h3>

                                            @if ($batch->location)
                                                <p class="text-xs text-neutral-600 flex items-center gap-1.5 pt-1">
                                                    <i class="fas fa-building text-neutral-400 text-xs"></i>
                                                    <span>{{ $batch->location->name }}, {{ $batch->location->address_line_1 }}, {{ $batch->location->city }} ({{ $batch->location->district }} District)</span>
                                                </p>
                                            @endif

                                            <div class="flex flex-wrap items-center gap-4 text-[11px] text-neutral-500 pt-1">
                                                @if ($batch->start_date)
                                                    <span><i class="far fa-calendar-alt text-lb-orange mr-1"></i> Starts: {{ $batch->start_date->format('d M Y') }}</span>
                                                @endif
                                                @if ($batch->end_date)
                                                    <span><i class="far fa-calendar-check text-neutral-400 mr-1"></i> Ends: {{ $batch->end_date->format('d M Y') }}</span>
                                                @endif
                                                <span><i class="fas fa-user-friends text-neutral-400 mr-1"></i> Seats: {{ $batch->availableSeats() }} of {{ $batch->max_seats }} remaining</span>
                                            </div>
                                        </div>

                                        <div class="shrink-0 text-left sm:text-right">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-white border border-neutral-200 text-neutral-700">
                                                Asia/Colombo Timezone
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Course Curriculum & Sections Accordion -->
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-5 sm:p-7 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-neutral-100 pb-4">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                    <i class="fas fa-list-ol text-lb-orange" aria-hidden="true"></i>
                                    <span>Course Curriculum</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-1">
                                    {{ $course->sections->count() }} Modules &bull; {{ $totalLessons }} Lessons &bull; {{ $totalDurationMinutes }} min total duration
                                </p>
                            </div>
                        </div>

                        @if ($course->sections->isEmpty())
                            <div class="p-8 text-center bg-neutral-50 rounded-2xl border border-neutral-100 text-xs text-neutral-500">
                                Detailed syllabus modules are currently being prepared for this course.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($course->sections as $index => $section)
                                    @php
                                        $cleanTitle = $section->clean_title ?? preg_replace('/^(?:module|section)\s*\d+\s*[:\-–—.]\s*/i', '', $section->title ?? '');
                                        $cleanTitle = trim($cleanTitle) !== '' ? trim($cleanTitle) : $section->title;
                                    @endphp
                                    <div class="border border-neutral-200 rounded-2xl overflow-hidden" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="w-full bg-neutral-50 px-4 sm:px-5 py-3 sm:py-3.5 flex items-center justify-between text-left cursor-pointer hover:bg-neutral-100/80 transition"
                                        >
                                            <div class="flex items-center gap-2.5 sm:gap-3 flex-1 min-w-0 pr-3 sm:pr-4">
                                                <i class="fas text-xs text-neutral-400 shrink-0 transition-transform duration-200" :class="open ? 'fa-chevron-down text-lb-orange' : 'fa-chevron-right'"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider mr-1.5 whitespace-nowrap">Module {{ $index + 1 }}:</span>
                                                    <h3 class="text-xs sm:text-[13px] font-bold text-neutral-900 inline leading-snug">{{ $cleanTitle }}</h3>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 sm:gap-3 text-xs text-neutral-500 shrink-0">
                                                <span>{{ $section->lessons->count() }} Lessons</span>
                                                <span class="hidden sm:inline">&bull;</span>
                                                <span class="hidden sm:inline">{{ $section->lessons->sum('duration_minutes') }} min</span>
                                            </div>
                                        </button>

                                        <div x-show="open" class="divide-y divide-neutral-100 p-2 sm:p-3 bg-white">
                                            @forelse ($section->lessons as $lesson)
                                                <div class="px-3 py-2.5 flex items-center justify-between hover:bg-neutral-50 rounded-xl transition">
                                                    <div class="flex items-center gap-3">
                                                        <i class="{{ $lesson->type === 'video' ? 'fas fa-play-circle text-blue-500' : 'fas fa-file-alt text-neutral-400' }} text-sm shrink-0" aria-hidden="true"></i>
                                                        <span class="text-xs font-medium text-neutral-800">{{ $lesson->title }}</span>
                                                        @if ($lesson->is_preview)
                                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                                                Free Preview
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="flex items-center gap-3 text-xs text-neutral-400 font-mono">
                                                        <span>{{ $lesson->duration_minutes }} min</span>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="p-3 text-center text-xs text-neutral-400">
                                                    Lessons in this module will be uploaded shortly.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Instructors Section -->
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-6">
                        <div class="border-b border-neutral-100 pb-4">
                            <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher text-lb-orange" aria-hidden="true"></i>
                                <span>Instructors &amp; Mentors</span>
                            </h2>
                        </div>

                        <div class="space-y-6">
                            @foreach ($course->instructors as $inst)
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="w-16 h-16 rounded-full bg-lb-peach text-lb-orange flex items-center justify-center font-extrabold text-lg shrink-0 border border-orange-200">
                                        {{ strtoupper(substr($inst->name, 0, 2)) }}
                                    </div>
                                    <div class="space-y-1.5 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-base font-bold text-neutral-900">
                                                <a href="{{ route('instructors.show', $inst) }}" class="hover:text-lb-orange transition">
                                                    {{ $inst->name }}
                                                </a>
                                            </h3>
                                            @if ($inst->pivot->is_primary)
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-900 text-white">
                                                    Lead Instructor
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-xs text-neutral-500 leading-relaxed">
                                            {{ $inst->bio ?: 'Certified technology educator and industry practitioner on LearnBuddy Sri Lanka.' }}
                                        </p>

                                        @if ($inst->city || $inst->province)
                                            <p class="text-[11px] text-neutral-400 flex items-center gap-1 pt-1">
                                                <i class="fas fa-map-pin text-lb-orange"></i>
                                                <span>{{ $inst->city ? $inst->city . ', ' : '' }}{{ $inst->province }} Province</span>
                                            </p>
                                        @endif

                                        <div class="pt-1">
                                            <a href="{{ route('instructors.show', $inst) }}" class="text-xs font-semibold text-lb-orange hover:underline">
                                                View Complete Profile &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Reviews & Student Feedback Section (Real data only, no invented ratings) -->
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-6">
                        @php
                            $pubReviews = $course->publishedReviews;
                            $totalPubCount = $pubReviews->count();
                            $realAvg = $totalPubCount > 0 ? round($pubReviews->avg('rating'), 1) : 0;
                            $starBreakdown = [
                                5 => $pubReviews->where('rating', 5)->count(),
                                4 => $pubReviews->where('rating', 4)->count(),
                                3 => $pubReviews->where('rating', 3)->count(),
                                2 => $pubReviews->where('rating', 2)->count(),
                                1 => $pubReviews->where('rating', 1)->count(),
                            ];

                            $currentUser = auth()->user();
                            $isEnrolled = $currentUser ? \App\Models\Enrollment::where('user_id', $currentUser->id)
                                ->where('course_id', $course->id)
                                ->whereIn('status', [\App\Models\Enrollment::STATUS_ACTIVE, \App\Models\Enrollment::STATUS_COMPLETED])
                                ->exists() : false;
                            $existingReview = $currentUser ? $course->reviews()->where('user_id', $currentUser->id)->first() : null;

                            $reviewsToDisplay = ($currentUser && $currentUser->isAdmin())
                                ? $course->reviews()->with('user')->latest()->get()
                                : $pubReviews;
                        @endphp

                        <!-- Header & Summary Stats -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-100 pb-5">
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                                    <i class="fas fa-star text-amber-500" aria-hidden="true"></i>
                                    <span>Verified Student Feedback &amp; Ratings</span>
                                </h2>
                                <p class="text-xs text-neutral-500 mt-0.5">Authentic course ratings submitted by verified enrolled Sri Lankan students.</p>
                            </div>

                            @if ($totalPubCount > 0)
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-2xl font-extrabold text-neutral-900 leading-tight">
                                            {{ number_format($realAvg, 1) }}<span class="text-xs font-normal text-neutral-400">/5</span>
                                        </div>
                                        <div class="flex text-amber-400 text-xs mt-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($realAvg) ? 'text-amber-400' : 'text-neutral-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-neutral-400 font-medium">({{ $totalPubCount }} {{ Str::plural('rating', $totalPubCount) }})</span>
                                </div>
                            @endif
                        </div>

                        <!-- Rating Breakdown Bars (If reviews exist) -->
                        @if ($totalPubCount > 0)
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5 justify-center flex flex-col">
                                    @for ($s = 5; $s >= 1; $s--)
                                        @php
                                            $count = $starBreakdown[$s];
                                            $pct = $totalPubCount > 0 ? round(($count / $totalPubCount) * 100) : 0;
                                        @endphp
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="w-12 text-neutral-500 text-[11px] font-medium shrink-0">{{ $s }} stars</span>
                                            <div class="flex-1 bg-neutral-200 rounded-full h-2 overflow-hidden">
                                                <div class="bg-amber-400 h-2 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="w-8 text-neutral-400 text-[10px] text-right shrink-0">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                                <div class="flex flex-col justify-center items-center sm:border-l sm:border-neutral-200 text-center px-4">
                                    <div class="text-3xl font-extrabold text-neutral-900">{{ number_format($realAvg, 1) }}</div>
                                    <div class="flex text-amber-400 text-sm my-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($realAvg) ? 'text-amber-400' : 'text-neutral-200' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-[11px] text-neutral-500">100% Verified Enrolled Learners</p>
                                    <p class="text-[10px] text-neutral-400">Strictly authentic. Zero fabricated ratings.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Student Review Form / User Review Management -->
                        @auth
                            @if ($existingReview)
                                <!-- User Already Left a Review: Display with Edit/Delete Controls -->
                                <div x-data="{ editing: false }" class="p-5 rounded-2xl border-2 border-orange-200 bg-orange-50/30 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-lb-orange uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fas fa-user-check"></i>
                                            <span>Your Course Review</span>
                                        </span>
                                        <div class="flex items-center gap-2" x-show="!editing">
                                            <button
                                                type="button"
                                                @click="editing = true"
                                                class="px-2.5 py-1 text-xs font-semibold text-neutral-700 hover:text-lb-orange bg-white border border-neutral-200 rounded-lg transition"
                                            >
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </button>
                                            <button
                                                type="button"
                                                data-action="{{ route('courses.reviews.destroy', [$course, $existingReview]) }}"
                                                onclick="confirmDialog({
                                                    title: 'Remove Review?',
                                                    message: 'Are you sure you want to remove your review for this course?',
                                                    action: this.dataset.action,
                                                    method: 'DELETE',
                                                    confirmText: 'Remove Review',
                                                    confirmVariant: 'danger'
                                                })"
                                                class="px-2.5 py-1 text-xs font-semibold text-red-600 hover:text-red-700 bg-white border border-neutral-200 rounded-lg transition cursor-pointer"
                                            >
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Read-only view -->
                                    <div x-show="!editing" class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex text-amber-400 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $existingReview->rating ? 'text-amber-400' : 'text-neutral-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-xs font-bold text-neutral-800">{{ $existingReview->rating }} / 5</span>
                                            <span class="text-[10px] text-neutral-400">&bull; {{ $existingReview->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if ($existingReview->title)
                                            <h4 class="text-xs font-bold text-neutral-900">{{ $existingReview->title }}</h4>
                                        @endif
                                        <p class="text-xs text-neutral-700 leading-relaxed">{{ $existingReview->comment }}</p>
                                    </div>

                                    <!-- Edit Form -->
                                    <form
                                        x-show="editing"
                                        x-cloak
                                        action="{{ route('courses.reviews.update', [$course, $existingReview]) }}"
                                        method="POST"
                                        class="space-y-3 pt-2"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-neutral-700 block">Rating</label>
                                            <select name="rating" required class="w-full sm:w-48 text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-orange-200">
                                                @for ($r = 5; $r >= 1; $r--)
                                                    <option value="{{ $r }}" {{ $existingReview->rating === $r ? 'selected' : '' }}>
                                                        {{ $r }} {{ Str::plural('Star', $r) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-neutral-700 block">Title (Optional)</label>
                                            <input
                                                type="text"
                                                name="title"
                                                value="{{ old('title', $existingReview->title) }}"
                                                maxlength="100"
                                                class="w-full text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-orange-200"
                                            >
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-neutral-700 block">Review Feedback</label>
                                            <textarea
                                                name="comment"
                                                rows="3"
                                                required
                                                minlength="10"
                                                maxlength="2000"
                                                class="w-full text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-orange-200"
                                            >{{ old('comment', $existingReview->comment) }}</textarea>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button
                                                type="submit"
                                                class="px-4 py-2 bg-lb-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-sm"
                                            >
                                                Update Review
                                            </button>
                                            <button
                                                type="button"
                                                @click="editing = false"
                                                class="px-3 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded-xl text-xs font-semibold transition"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            @elseif ($isEnrolled)
                                <!-- Enrolled Student Without Existing Review: Submission Form -->
                                <div x-data="{ rating: 5 }" class="p-5 sm:p-6 rounded-2xl border border-neutral-200 bg-neutral-50/70 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="space-y-0.5">
                                            <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                                <i class="fas fa-pen text-lb-orange"></i>
                                                <span>Write a Verified Course Review</span>
                                            </h3>
                                            <p class="text-xs text-neutral-500">As an enrolled student, your authentic feedback helps other Sri Lankan learners.</p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                            Enrolled Student
                                        </span>
                                    </div>

                                    <form action="{{ route('courses.reviews.store', $course) }}" method="POST" class="space-y-4">
                                        @csrf

                                        <!-- Interactive Star Rating -->
                                        <div class="space-y-1">
                                            <label class="text-xs font-bold text-neutral-700 block">Your Rating <span class="text-red-500">*</span></label>
                                            <div class="flex items-center gap-2">
                                                <input type="hidden" name="rating" :value="rating">
                                                <div class="flex items-center gap-1 text-xl cursor-pointer">
                                                    <template x-for="star in 5" :key="star">
                                                        <button
                                                            type="button"
                                                            @click="rating = star"
                                                            class="text-neutral-300 hover:text-amber-400 focus:outline-none transition-colors"
                                                            :class="{ 'text-amber-400': star <= rating, 'text-neutral-300': star > rating }"
                                                        >
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    </template>
                                                </div>
                                                <span class="text-xs font-bold text-neutral-600 ml-2" x-text="rating + ' / 5 Stars'"></span>
                                            </div>
                                        </div>

                                        <!-- Review Title -->
                                        <div class="space-y-1">
                                            <label for="review_title" class="text-xs font-bold text-neutral-700 block">Headline / Summary (Optional)</label>
                                            <input
                                                type="text"
                                                id="review_title"
                                                name="title"
                                                value="{{ old('title') }}"
                                                placeholder="e.g. Excellent practical insights and clear explanations"
                                                maxlength="100"
                                                class="w-full text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-orange-200"
                                            >
                                        </div>

                                        <!-- Review Comment -->
                                        <div class="space-y-1">
                                            <label for="review_comment" class="text-xs font-bold text-neutral-700 block">Detailed Review <span class="text-red-500">*</span></label>
                                            <textarea
                                                id="review_comment"
                                                name="comment"
                                                rows="3"
                                                required
                                                minlength="10"
                                                maxlength="2000"
                                                placeholder="Share what you liked, how the lectures and exercises helped your career or learning, and any tips for future students..."
                                                class="w-full text-xs rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-orange-200"
                                            >{{ old('comment') }}</textarea>
                                            <p class="text-[10px] text-neutral-400">Minimum 10 characters. Safe and respectful reviews only.</p>
                                        </div>

                                        <button
                                            type="submit"
                                            class="px-5 py-2.5 bg-lb-orange hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-2"
                                        >
                                            <i class="fas fa-paper-plane text-[10px]"></i>
                                            <span>Post Verified Review</span>
                                        </button>
                                    </form>
                                </div>

                            @else
                                <!-- Logged in, but not enrolled -->
                                <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200 text-xs text-neutral-500 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-neutral-200 text-neutral-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <p>Enrolled students can leave verified reviews for this course. Enroll today to participate and share feedback.</p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Guest User -->
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200 text-xs text-neutral-600 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-neutral-200 text-neutral-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-user-lock"></i>
                                    </div>
                                    <p>Are you enrolled in this course? <a href="{{ route('login') }}" class="font-bold text-lb-orange hover:underline">Log in</a> to submit your authentic verified feedback.</p>
                                </div>
                            </div>
                        @endauth

                        <!-- Reviews List -->
                        @if ($reviewsToDisplay->isEmpty())
                            <div class="p-8 text-center bg-neutral-50 rounded-2xl border border-neutral-100 text-xs text-neutral-500 space-y-2">
                                <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto text-neutral-400">
                                    <i class="far fa-comment-dots text-base"></i>
                                </div>
                                <p class="font-medium text-neutral-800">No reviews yet for this course.</p>
                                <p class="text-neutral-400 max-w-sm mx-auto">
                                    Enrolled students will submit authentic reviews as they progress through coursework.
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($reviewsToDisplay as $rev)
                                    <div class="p-4 rounded-2xl border border-neutral-100 bg-neutral-50/50 space-y-3 {{ ! $rev->is_published ? 'border-amber-300 bg-amber-50/40' : '' }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-orange-100 text-lb-orange flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($rev->user?->name ?? 'Student', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="text-xs font-bold text-neutral-900">{{ $rev->user?->name ?? 'Verified Student' }}</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100/70 px-1.5 py-0.2 rounded-full">
                                                            <i class="fas fa-check-circle text-[9px]"></i> Verified Learner
                                                        </span>
                                                        @if (! $rev->is_published)
                                                            <span class="text-[10px] font-bold text-amber-800 bg-amber-200 px-1.5 py-0.2 rounded-full">
                                                                [Hidden from Public]
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-[10px] text-neutral-400 block">{{ $rev->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="flex text-amber-400 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $rev->rating ? 'text-amber-400' : 'text-neutral-300' }}"></i>
                                                @endfor
                                            </div>
                                        </div>

                                        @if ($rev->title)
                                            <h4 class="text-xs font-bold text-neutral-900">{{ $rev->title }}</h4>
                                        @endif

                                        <p class="text-xs text-neutral-600 leading-relaxed">{{ $rev->comment }}</p>

                                        <!-- Administrator Moderation Controls -->
                                        @if (auth()->check() && auth()->user()->isAdmin())
                                            <div class="pt-2 border-t border-neutral-200/60 flex items-center justify-between text-xs">
                                                <span class="text-[10px] font-mono text-neutral-400">Admin Moderation</span>
                                                <div class="flex items-center gap-2">
                                                    <form action="{{ route('courses.reviews.moderate', [$course, $rev]) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="is_published" value="{{ $rev->is_published ? '0' : '1' }}">
                                                        <button
                                                            type="submit"
                                                            class="px-2.5 py-1 rounded text-[11px] font-semibold transition {{ $rev->is_published ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}"
                                                        >
                                                            {{ $rev->is_published ? 'Hide Review' : 'Publish Review' }}
                                                        </button>
                                                    </form>

                                                    <button
                                                        type="button"
                                                        data-item="Review by {{ $rev->user->name }}"
                                                        data-action="{{ route('courses.reviews.destroy', [$course, $rev]) }}"
                                                        onclick="confirmDialog({
                                                            title: 'Delete Review?',
                                                            itemName: this.dataset.item,
                                                            message: 'Permanently delete this inappropriate review from the course discussion?',
                                                            action: this.dataset.action,
                                                            method: 'DELETE',
                                                            confirmText: 'Delete Review',
                                                            confirmVariant: 'danger'
                                                        })"
                                                        class="px-2.5 py-1 rounded text-[11px] font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition cursor-pointer"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT STICKY SIDEBAR (4 Cols) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                    <!-- Pricing & Enrollment Box -->
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-md p-6 space-y-5">
                        <!-- Preview Thumbnail -->
                        <div class="relative h-48 rounded-2xl bg-gradient-to-tr from-neutral-900 to-neutral-800 overflow-hidden shadow-sm">
                            @if ($course->thumbnail_url)
                                <img
                                    src="{{ $course->thumbnail_url }}"
                                    alt="{{ $course->title }}"
                                    class="w-full h-full object-cover"
                                    onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';"
                                >
                                <div class="w-full h-full items-center justify-center bg-gradient-to-tr from-orange-100 via-white to-neutral-100 hidden">
                                    <i class="{{ $course->category?->icon ?? 'fas fa-graduation-cap' }} text-5xl text-lb-orange/40" aria-hidden="true"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-orange-100 via-white to-neutral-100">
                                    <i class="{{ $course->category?->icon ?? 'fas fa-graduation-cap' }} text-5xl text-lb-orange/40" aria-hidden="true"></i>
                                </div>
                            @endif

                            @if ($previewLessons->isNotEmpty())
                                <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-[2px] flex items-center justify-center">
                                    <span class="px-4 py-2 rounded-full bg-white/90 text-neutral-900 text-xs font-bold flex items-center gap-2 shadow-lg">
                                        <i class="fas fa-play text-lb-orange text-xs"></i>
                                        <span>Preview Lessons Available</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Tuition Fee Range -->
                        <div class="pt-2 border-b border-neutral-100 pb-4">
                            <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider block">Tuition Fee</span>
                            <div class="text-3xl font-extrabold text-lb-black mt-1">
                                {{ $course->formattedPriceRange() }}
                            </div>
                            <p class="text-[11px] text-neutral-500 mt-1">
                                Standard pricing across Sri Lanka. Includes all taxes where applicable.
                            </p>
                        </div>

                        <!-- Enrollment Actions -->
                        <div class="space-y-3">
                            @auth
                                @if (auth()->user()->isEnrolledIn($course))
                                    @php $userEnrollment = auth()->user()->getEnrollmentFor($course); @endphp
                                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-2.5">
                                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                                            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                            <span>You are enrolled in this course</span>
                                        </div>
                                        <p class="text-[11px] text-emerald-700">
                                            Status: <span class="uppercase font-bold tracking-wider">{{ str_replace('_', ' ', $userEnrollment->status) }}</span>
                                        </p>
                                        <div class="pt-1 flex flex-col gap-2">
                                            @if ($userEnrollment->status === \App\Models\Enrollment::STATUS_ACTIVE)
                                                <x-button as="a" href="{{ route('learning.index', $course) }}" variant="primary" class="w-full text-center">
                                                    <i class="fas fa-play text-xs mr-1"></i> Start Learning Now
                                                </x-button>
                                            @endif
                                            <x-button as="a" href="{{ route('student.dashboard') }}" variant="outline" class="w-full text-center text-xs">
                                                Go to Student Dashboard
                                            </x-button>
                                            <a href="{{ route('enrollments.confirmation', $userEnrollment) }}" class="text-xs text-neutral-600 hover:text-lb-orange underline">
                                                View Enrollment Receipt
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <x-button
                                            as="a"
                                            href="{{ route('checkout.show', $course) }}"
                                            variant="primary"
                                            size="lg"
                                            class="w-full text-center font-bold"
                                        >
                                            Enroll / Checkout &bull; {{ $course->formattedPriceRange() }}
                                        </x-button>
                                        <p class="text-[11px] text-neutral-500 text-center">
                                            Select your delivery mode, campus batch &amp; pay securely via PayHere.
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="space-y-2">
                                    <x-button
                                        as="a"
                                        href="{{ route('register') }}"
                                        variant="primary"
                                        size="lg"
                                        class="w-full text-center"
                                    >
                                        Register to Enroll
                                    </x-button>

                                    <x-button
                                        as="a"
                                        href="{{ route('login') }}"
                                        variant="outline"
                                        size="md"
                                        class="w-full text-center"
                                    >
                                        Existing Student? Log In
                                    </x-button>
                                </div>
                            @endauth
                        </div>

                        <!-- Value Props Checklist -->
                        <div class="pt-4 border-t border-neutral-100 space-y-2.5 text-xs text-neutral-600">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-infinity text-lb-orange text-xs shrink-0"></i>
                                <span>Lifetime online access &amp; lesson resources</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-certificate text-emerald-500 text-xs shrink-0"></i>
                                <span>Verifiable digital completion certificate</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-mobile-alt text-blue-500 text-xs shrink-0"></i>
                                <span>Access on mobile, tablet, and desktop</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-shield-alt text-neutral-400 text-xs shrink-0"></i>
                                <span>Secure encrypted platform (TLS 1.3)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RELATED COURSES SECTION -->
            @if ($relatedCourses->isNotEmpty())
                <div class="mt-16 pt-12 border-t border-neutral-200/80 space-y-6">
                    <div>
                        <x-badge variant="orange" icon="fas fa-layer-group" class="mb-2">
                            Related Curriculums
                        </x-badge>
                        <h2 class="text-xl sm:text-2xl font-bold text-neutral-900">
                            More Courses in {{ $course->category?->name ?? 'this Field' }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($relatedCourses as $rel)
                            <x-course-card :course="$rel" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
