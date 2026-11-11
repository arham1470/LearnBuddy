<x-layouts.public title="Live Classes - {{ $course->title }} - LearnBuddy" description="View upcoming and past interactive video class sessions.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('student.dashboard') }}" class="hover:text-lb-orange">Student Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('learning.index', $course->slug) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 20) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Live Classes</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-video text-xs" aria-hidden="true"></i> Interactive Virtual Classes
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        Live Class Schedule
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Course: <strong>{{ $course->title }}</strong> &bull; All times shown in <strong>Asia/Colombo</strong>.
                    </p>
                </div>

                <a
                    href="{{ route('learning.index', $course->slug) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition shadow-sm self-start md:self-auto"
                >
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                    <span>Back to Learning Portal</span>
                </a>
            </div>

            <!-- Sessions List -->
            @if ($liveClasses->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($liveClasses as $cls)
                        @php
                            $user = auth()->user();
                            $timeStatus = $cls->timeWindowStatus($user);
                            $canJoin = $cls->canBeJoinedBy($user);
                        @endphp
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition">
                            <div class="space-y-2 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($cls->isLive())
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse border border-emerald-300">
                                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                            LIVE NOW
                                        </span>
                                    @elseif ($cls->isScheduled())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fas fa-clock text-[10px]"></i> Scheduled
                                        </span>
                                    @elseif ($cls->isCompleted())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            <i class="fas fa-check text-[10px]"></i> Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-times text-[10px]"></i> Cancelled
                                        </span>
                                    @endif

                                    <span class="text-xs font-medium text-neutral-500">
                                        Host: <strong>{{ $cls->instructor->name }}</strong>
                                    </span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-neutral-900 line-clamp-1">
                                    {{ $cls->title }}
                                </h2>

                                @if ($cls->description)
                                    <p class="text-xs text-neutral-500 line-clamp-2">
                                        {{ $cls->description }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-500 pt-1">
                                    <span>
                                        <i class="fas fa-calendar-day text-neutral-400 mr-1"></i>
                                        {{ $cls->formattedDate() }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>
                                        <i class="fas fa-clock text-neutral-400 mr-1"></i>
                                        {{ $cls->formattedTiming() }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 shrink-0">
                                <a
                                    href="{{ route('courses.live-classes.show', [$course->slug, $cls]) }}"
                                    class="px-4 py-2 rounded-full border border-neutral-300 hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition"
                                >
                                    Session Details
                                </a>

                                @if ($canJoin)
                                    <a
                                        href="{{ route('courses.live-classes.join', [$course->slug, $cls]) }}"
                                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                                    >
                                        <i class="fas fa-video text-xs"></i>
                                        <span>Join Class Now</span>
                                    </a>
                                @elseif ($timeStatus === 'not_started')
                                    <span class="px-4 py-2 rounded-full bg-neutral-100 text-neutral-500 text-xs font-medium">
                                        Opens 15m before start
                                    </span>
                                @elseif ($timeStatus === 'ended')
                                    <span class="px-4 py-2 rounded-full bg-neutral-100 text-neutral-500 text-xs font-medium">
                                        Session Closed
                                    </span>
                                @elseif ($timeStatus === 'cancelled')
                                    <span class="px-4 py-2 rounded-full bg-rose-50 text-rose-700 text-xs font-medium">
                                        Cancelled
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-12 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-xl">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-base font-bold text-neutral-900">No Live Classes Scheduled</h3>
                    <p class="text-xs text-neutral-500 max-w-sm mx-auto leading-relaxed">
                        There are currently no live interactive video workshops scheduled for this course. Your instructor will announce upcoming sessions here.
                    </p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
