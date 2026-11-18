<x-layouts.public title="Host Console - {{ $liveClass->title }} - LearnBuddy" description="Host management console for live video classes.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 20) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.live-classes.index', $course) }}" class="hover:text-lb-orange">Live Classes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">{{ Str::limit($liveClass->title, 20) }}</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Provider Notice -->
            @if (! $isProviderConfigured)
                <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm text-amber-900">
                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                        <span>Meeting Provider Unconfigured</span>
                    </div>
                    <p class="leading-relaxed text-amber-700">
                        {{ $unconfiguredReason }}
                    </p>
                    <p class="text-[11px] font-medium text-amber-800">
                        Instructors and students attempting to join will be shown a clear setup instructions screen rather than a broken meeting.
                    </p>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-neutral-900 text-white">
                                {{ strtoupper($liveClass->provider) }}
                            </span>

                            @if ($liveClass->isLive())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse border border-emerald-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    LIVE NOW
                                </span>
                            @elseif ($liveClass->isScheduled())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fas fa-clock text-[10px]"></i> Scheduled
                                </span>
                            @elseif ($liveClass->isCompleted())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <i class="fas fa-check text-[10px]"></i> Completed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fas fa-times text-[10px]"></i> Cancelled
                                </span>
                            @endif

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700">
                                {{ $liveClass->batch ? 'Batch: '.$liveClass->batch->batch_code : 'All Enrolled Learners' }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                            {{ $liveClass->title }}
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-500">
                            Course: <strong>{{ $course->title }}</strong> &bull; Host: <strong>{{ $liveClass->instructor->name }}</strong>
                        </p>
                    </div>

                    <!-- Host Primary Action Controls -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        @if (! $liveClass->isCancelled() && ! $liveClass->isCompleted())
                            <a
                                href="{{ route('courses.live-classes.join', [$course->slug, $liveClass]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                            >
                                <i class="fas fa-video text-xs"></i>
                                <span>Launch Room as Host</span>
                            </a>
                        @endif

                        @can('update', $liveClass)
                            @if ($liveClass->isLive())
                                <form id="mgmt-end-live-form" method="POST" action="{{ route('management.courses.live-classes.end', [$course, $liveClass]) }}" class="inline">
                                    @csrf
                                    <button
                                        type="button"
                                        data-item="{{ $liveClass->title }}"
                                        onclick="confirmDialog({
                                            title: 'End Live Session?',
                                            itemName: this.dataset.item,
                                            message: 'Are you sure you want to end this live session for all participants? It will be marked as Completed.',
                                            targetForm: document.getElementById('mgmt-end-live-form'),
                                            confirmText: 'End Session for All',
                                            confirmVariant: 'danger',
                                            icon: 'fas fa-phone-slash'
                                        })"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                                    >
                                        <i class="fas fa-phone-slash text-xs"></i>
                                        <span>End Live Session</span>
                                    </button>
                                </form>
                            @endif

                            @if (! $liveClass->isCompleted())
                                <a
                                    href="{{ route('management.courses.live-classes.edit', [$course, $liveClass]) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full border border-neutral-300 bg-white hover:bg-neutral-50 text-xs font-semibold text-neutral-700 transition shadow-sm"
                                >
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Edit</span>
                                </a>
                            @endif

                            @if (! $liveClass->isCancelled() && ! $liveClass->isCompleted())
                                <button
                                    type="button"
                                    onclick="document.getElementById('cancel-session-dialog').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition cursor-pointer"
                                >
                                    <i class="fas fa-ban text-xs"></i>
                                    <span>Cancel Session</span>
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>

                <!-- Session Meta Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-neutral-100">
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Scheduled Timing</span>
                        <span class="text-xs font-bold text-neutral-900 mt-0.5 block">
                            {{ $liveClass->formattedDate() }}
                        </span>
                        <span class="text-[11px] text-neutral-500 mt-0.5 block">
                            {{ $liveClass->formattedTiming() }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Meeting Identifier</span>
                        <span class="text-xs font-mono font-bold text-neutral-800 mt-0.5 block truncate">
                            {{ $liveClass->meeting_identifier }}
                        </span>
                        <span class="text-[10px] text-neutral-400 mt-0.5 block">
                            Provider: Jitsi Meet SDK
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Audience Permission</span>
                        <span class="text-xs font-bold text-neutral-900 mt-0.5 block">
                            {{ ucfirst(str_replace('_', ' ', $liveClass->access_permission)) }}
                        </span>
                        <span class="text-[10px] text-neutral-400 mt-0.5 block">
                            Enrolled learners only
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Reminder Notifications</span>
                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-[11px] text-neutral-600">
                                {{ $liveClass->reminder_sent_at ? 'Sent '.$liveClass->reminder_sent_at->diffForHumans() : 'None sent' }}
                            </span>
                            @if (! $liveClass->isCancelled() && ! $liveClass->isCompleted())
                                <form method="POST" action="{{ route('management.courses.live-classes.reminder', [$course, $liveClass]) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="px-2.5 py-1 rounded-lg bg-orange-100 hover:bg-orange-200 text-lb-orange text-[10px] font-bold transition cursor-pointer"
                                    >
                                        Send Reminder
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($liveClass->description)
                    <div class="mt-6 pt-6 border-t border-neutral-100 space-y-2">
                        <h3 class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Agenda &amp; Discussion Topics</h3>
                        <p class="text-xs text-neutral-600 leading-relaxed whitespace-pre-line bg-neutral-50/70 p-4 rounded-2xl border border-neutral-100">
                            {{ $liveClass->description }}
                        </p>
                    </div>
                @endif

                @if ($liveClass->isCancelled())
                    <div class="mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <span class="font-bold">Cancellation Reason:</span>
                        <p class="italic">{{ $liveClass->cancellation_reason ?? 'Session cancelled by instructor.' }}</p>
                    </div>
                @endif
            </div>

            <!-- Cancel Session Modal -->
            <div id="cancel-session-dialog" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl">
                    <div class="flex items-center gap-3 text-rose-600">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-lg">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-900">Cancel Live Class</h3>
                            <p class="text-xs text-neutral-500">Provide an explanation for enrolled students.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('management.courses.live-classes.cancel', [$course, $liveClass]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="cancellation_reason" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Reason for Cancellation <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="cancellation_reason"
                                name="cancellation_reason"
                                rows="3"
                                required
                                placeholder="Explain why the session is being cancelled or when it will be rescheduled..."
                                class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="button"
                                onclick="document.getElementById('cancel-session-dialog').classList.add('hidden')"
                                class="px-4 py-2 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition cursor-pointer"
                            >
                                Close
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition cursor-pointer"
                            >
                                Confirm Cancellation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
