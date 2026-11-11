<x-layouts.public title="{{ $liveClass->title }} - Live Class - LearnBuddy" description="Interactive live video class session details, agenda, and countdown.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('student.dashboard') }}" class="hover:text-lb-orange">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('courses.live-classes.index', $course->slug) }}" class="hover:text-lb-orange">Live Classes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">{{ Str::limit($liveClass->title, 25) }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800">
                        <i class="fas fa-video text-xs"></i> Interactive Session
                    </span>

                    @if ($liveClass->isLive())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse border border-emerald-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            LIVE NOW
                        </span>
                    @elseif ($liveClass->isScheduled())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <i class="fas fa-clock text-[10px]"></i> Scheduled
                        </span>
                    @elseif ($liveClass->isCompleted())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            <i class="fas fa-check text-[10px]"></i> Completed
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            <i class="fas fa-times text-[10px]"></i> Cancelled
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                    {{ $liveClass->title }}
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600">
                    Course: <a href="{{ route('learning.index', $course->slug) }}" class="font-semibold text-neutral-900 hover:text-lb-orange underline">{{ $course->title }}</a>
                </p>

                <!-- Scheduled Timing Bar -->
                <div class="pt-4 border-t border-neutral-100 flex flex-wrap items-center gap-6 text-xs text-neutral-600">
                    <div>
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase">Date</span>
                        <span class="font-bold text-neutral-900 mt-0.5 block">{{ $liveClass->formattedDate() }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase">Time</span>
                        <span class="font-bold text-neutral-900 mt-0.5 block">{{ $liveClass->formattedTiming() }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase">Conducted By</span>
                        <span class="font-bold text-neutral-900 mt-0.5 block">{{ $liveClass->instructor->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Countdown / Action Banner -->
            <div
                x-data="{
                    target: new Date('{{ $liveClass->scheduled_start_time->toISOString() }}').getTime(),
                    now: new Date().getTime(),
                    diff: 0,
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0,
                    timer: null,
                    init() {
                        this.update();
                        this.timer = setInterval(() => this.update(), 1000);
                    },
                    update() {
                        this.now = new Date().getTime();
                        this.diff = Math.max(0, this.target - this.now);
                        this.days = Math.floor(this.diff / (1000 * 60 * 60 * 24));
                        this.hours = Math.floor((this.diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        this.minutes = Math.floor((this.diff % (1000 * 60 * 60)) / (1000 * 60));
                        this.seconds = Math.floor((this.diff % (1000 * 60)) / 1000);
                    }
                }"
                class="bg-gradient-to-br from-neutral-900 to-neutral-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg space-y-6"
            >
                @if ($canJoin)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 mb-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                Session Open &amp; Ready
                            </span>
                            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">The Live Class is Now Open</h2>
                            <p class="text-xs text-neutral-300 mt-1">
                                Audio and video controls are configured. Click below to enter the interactive meeting room.
                            </p>
                        </div>

                        <a
                            href="{{ route('courses.live-classes.join', [$course->slug, $liveClass]) }}"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-sm shadow-xl transition shrink-0"
                        >
                            <i class="fas fa-video text-sm"></i>
                            <span>Enter Live Meeting Room</span>
                        </a>
                    </div>
                @elseif ($timeStatus === 'not_started')
                    <div class="space-y-4 text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-xs font-bold text-orange-400 uppercase tracking-wider">Starts In</span>
                                <h2 class="text-lg font-bold text-white mt-0.5">Upcoming Live Workshop</h2>
                                <p class="text-xs text-neutral-400">Classroom room access opens 15 minutes prior to start time.</p>
                            </div>

                            <!-- Live Countdown Blocks -->
                            <div class="flex items-center justify-center gap-3">
                                <div class="px-3 py-2 rounded-2xl bg-white/10 text-center min-w-[55px]">
                                    <span class="text-lg sm:text-xl font-extrabold text-white block" x-text="days">0</span>
                                    <span class="text-[9px] uppercase tracking-wider text-neutral-400">Days</span>
                                </div>
                                <div class="px-3 py-2 rounded-2xl bg-white/10 text-center min-w-[55px]">
                                    <span class="text-lg sm:text-xl font-extrabold text-white block" x-text="hours">0</span>
                                    <span class="text-[9px] uppercase tracking-wider text-neutral-400">Hours</span>
                                </div>
                                <div class="px-3 py-2 rounded-2xl bg-white/10 text-center min-w-[55px]">
                                    <span class="text-lg sm:text-xl font-extrabold text-white block" x-text="minutes">0</span>
                                    <span class="text-[9px] uppercase tracking-wider text-neutral-400">Mins</span>
                                </div>
                                <div class="px-3 py-2 rounded-2xl bg-white/10 text-center min-w-[55px]">
                                    <span class="text-lg sm:text-xl font-extrabold text-lb-orange block" x-text="seconds">0</span>
                                    <span class="text-[9px] uppercase tracking-wider text-neutral-400">Secs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($timeStatus === 'cancelled')
                    <div class="p-4 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-200 text-xs space-y-1">
                        <span class="font-bold text-white">This class session has been cancelled.</span>
                        <p class="text-neutral-300">{{ $liveClass->cancellation_reason ?? 'Your instructor has cancelled this scheduled session.' }}</p>
                    </div>
                @else
                    <div class="text-center py-4 space-y-1">
                        <p class="text-sm font-bold text-white">This live session has concluded.</p>
                        <p class="text-xs text-neutral-400">Thank you for attending. Recorded summaries and materials will be available in the curriculum.</p>
                    </div>
                @endif
            </div>

            <!-- Agenda Details Card -->
            @if ($liveClass->description)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-3">
                    <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-list-ul text-lb-orange"></i>
                        <span>Classroom Agenda &amp; Discussion Topics</span>
                    </h3>
                    <div class="text-xs sm:text-sm text-neutral-700 leading-relaxed whitespace-pre-line">
                        {{ $liveClass->description }}
                    </div>
                </div>
            @endif

            <!-- Mobile & System Readiness Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 text-xs text-neutral-500 space-y-2">
                <span class="font-bold text-neutral-800 uppercase tracking-wider text-[11px] block">
                    <i class="fas fa-mobile-alt mr-1 text-neutral-400"></i> Supported Browsers &amp; Devices
                </span>
                <p class="leading-relaxed">
                    Live video sessions run directly in your web browser via standard WebRTC without requiring external desktop software installations.
                    Supported on modern desktop and mobile browsers (Google Chrome, Apple Safari on iOS 14+, Mozilla Firefox, Microsoft Edge). Please allow microphone and camera access permissions when prompted.
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>
