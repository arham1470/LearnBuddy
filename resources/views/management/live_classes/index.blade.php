<x-layouts.public title="Live Classes - {{ $course->title }} - LearnBuddy" description="Manage interactive live video classes, scheduling, and student access.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 25) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Live Classes</span>
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

            <!-- Provider Status Notice -->
            @if (! $isProviderConfigured)
                <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm text-amber-900">
                        <i class="fas fa-info-circle text-amber-600 text-base"></i>
                        <span>Live Meeting Provider Notice (Jitsi Meet Unconfigured)</span>
                    </div>
                    <p class="leading-relaxed text-amber-700">
                        {{ $unconfiguredReason }}
                    </p>
                    <div class="pt-1 flex flex-wrap gap-2 text-[11px] font-medium text-amber-800">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-100/80">&bull; Sessions can be scheduled and managed in advance.</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-100/80">&bull; Video rooms will show an informative setup state until provider credentials are set.</span>
                    </div>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-video text-xs" aria-hidden="true"></i> Interactive Virtual Classes
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        Live Video Classes
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Schedule and conduct live interactive workshops and Q&amp;A sessions for <strong>{{ $course->title }}</strong>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route('management.courses.show', $course) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full border border-neutral-300 bg-white text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-sm"
                    >
                        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                        <span>Course Console</span>
                    </a>

                    @can('create', [\App\Models\LiveClass::class, $course])
                        <a
                            href="{{ route('management.courses.live-classes.create', $course) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Schedule Live Class</span>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Sessions List / Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($liveClasses->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Session Title &amp; Target</th>
                                    <th class="p-4">Host Instructor</th>
                                    <th class="p-4">Scheduled Timing (Asia/Colombo)</th>
                                    <th class="p-4">Provider &amp; Room</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($liveClasses as $session)
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <!-- Title & Target -->
                                        <td class="p-4">
                                            <a href="{{ route('management.courses.live-classes.show', [$course, $session]) }}" class="font-bold text-neutral-900 hover:text-lb-orange transition text-sm">
                                                {{ $session->title }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if ($session->batch)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                                        Batch: {{ $session->batch->batch_code }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                                        All Enrolled Students
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Host Instructor -->
                                        <td class="p-4 whitespace-nowrap">
                                            <div class="font-medium text-neutral-900">{{ $session->instructor->name }}</div>
                                            <div class="text-[11px] text-neutral-400">{{ $session->instructor->email }}</div>
                                        </td>

                                        <!-- Scheduled Timing -->
                                        <td class="p-4 whitespace-nowrap">
                                            <div class="font-bold text-neutral-900">
                                                {{ $session->formattedDate() }}
                                            </div>
                                            <div class="text-[11px] text-neutral-500 mt-0.5">
                                                {{ $session->formattedTiming() }}
                                            </div>
                                        </td>

                                        <!-- Provider & Room -->
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-mono text-[10px] font-bold uppercase bg-neutral-100 text-neutral-800">
                                                {{ $session->provider }}
                                            </span>
                                            <div class="text-[10px] text-neutral-400 font-mono mt-0.5 truncate max-w-[140px]">
                                                {{ $session->meeting_identifier }}
                                            </div>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="p-4 whitespace-nowrap">
                                            @if ($session->isLive())
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 animate-pulse border border-emerald-300">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                                    LIVE NOW
                                                </span>
                                            @elseif ($session->isScheduled())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <i class="fas fa-clock text-[9px]"></i> Scheduled
                                                </span>
                                            @elseif ($session->isCompleted())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <i class="fas fa-check text-[9px]"></i> Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <i class="fas fa-times text-[9px]"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="p-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('management.courses.live-classes.show', [$course, $session]) }}"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-neutral-900 hover:bg-lb-orange text-white font-bold text-xs transition shadow-sm"
                                                    title="Open Session Management Console"
                                                >
                                                    <i class="fas fa-desktop text-xs"></i>
                                                    <span>Console</span>
                                                </a>

                                                @can('update', $session)
                                                    <a
                                                        href="{{ route('management.courses.live-classes.edit', [$course, $session]) }}"
                                                        class="p-2 rounded-lg text-neutral-500 hover:text-lb-orange hover:bg-orange-50 transition"
                                                        title="Edit / Reschedule"
                                                    >
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $session)
                                                    @if ($session->isScheduled())
                                                        <form method="POST" action="{{ route('management.courses.live-classes.destroy', [$course, $session]) }}" onsubmit="return confirm('Remove live class {{ $session->title }}?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                                title="Delete Session"
                                                            >
                                                                <i class="fas fa-trash-alt text-xs"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl">
                            <i class="fas fa-video" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-neutral-800">No Live Classes Scheduled</h3>
                        <p class="text-xs text-neutral-500 max-w-md mx-auto leading-relaxed">
                            No interactive video classes have been scheduled for this course yet. Schedule a live workshop to conduct real-time lessons, student code reviews, and interactive Q&amp;A sessions.
                        </p>
                        @can('create', [\App\Models\LiveClass::class, $course])
                            <div class="pt-2">
                                <a
                                    href="{{ route('management.courses.live-classes.create', $course) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                                >
                                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                                    <span>Schedule First Live Class</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
