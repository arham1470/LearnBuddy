<x-layouts.public title="Batches - {{ $course->title }} - LearnBuddy" description="Manage physical campus batches, student capacity, schedules, and attendance.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 25) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Batches &amp; Schedules</span>
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

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-building text-xs" aria-hidden="true"></i> Campus Operations
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        Physical Course Batches
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Manage classroom intakes, seat capacities, Sri Lankan training venues, and attendance for <strong>{{ $course->title }}</strong>.
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

                    @can('create', [\App\Models\CourseBatch::class, $course])
                        <a
                            href="{{ route('management.courses.batches.create', $course) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Create New Batch</span>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Batches Table / List -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($batches->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Batch Code &amp; Name</th>
                                    <th class="p-4">Campus Location</th>
                                    <th class="p-4">Delivery Mode</th>
                                    <th class="p-4">Capacity &amp; Seats</th>
                                    <th class="p-4">Timeline (Asia/Colombo)</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($batches as $batch)
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <!-- Code & Name -->
                                        <td class="p-4">
                                            <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}" class="font-bold text-neutral-900 hover:text-lb-orange transition text-sm">
                                                {{ $batch->batch_name }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-[11px] font-bold bg-neutral-100 text-neutral-800">
                                                    {{ $batch->batch_code }}
                                                </span>
                                                <span class="text-[11px] text-neutral-400">
                                                    {{ $batch->sessions_count }} sessions
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Campus Location -->
                                        <td class="p-4">
                                            @if ($batch->location)
                                                <div class="font-medium text-neutral-900">{{ $batch->location->name }}</div>
                                                <div class="text-[11px] text-neutral-500">
                                                    <i class="fas fa-map-pin text-[10px] text-neutral-400 mr-0.5"></i>
                                                    {{ $batch->location->city }}, {{ $batch->location->district }}
                                                </div>
                                            @else
                                                <span class="text-neutral-400 italic">No campus assigned</span>
                                            @endif
                                        </td>

                                        <!-- Delivery Mode -->
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $batch->deliveryOption?->delivery_mode === 'hybrid' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $batch->deliveryOption?->delivery_mode ?? 'Physical' }}
                                            </span>
                                            @if ($batch->deliveryOption)
                                                <div class="text-[11px] text-neutral-500 mt-0.5 font-medium">
                                                    {{ $batch->deliveryOption->title }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Capacity & Seats -->
                                        <td class="p-4">
                                            @php
                                                $pct = $batch->max_seats > 0 ? min(100, round(($batch->enrolled_seats / $batch->max_seats) * 100)) : 0;
                                            @endphp
                                            <div class="flex items-center justify-between text-[11px] font-semibold text-neutral-800 mb-1">
                                                <span>{{ $batch->enrolled_seats }} / {{ $batch->max_seats }} seats</span>
                                                <span class="{{ $batch->isFull() ? 'text-rose-600 font-bold' : 'text-neutral-500' }}">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-28 bg-neutral-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full {{ $batch->isFull() ? 'bg-rose-500' : ($pct > 75 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            @if ($batch->isFull())
                                                <span class="inline-block mt-1 text-[10px] text-rose-600 font-bold uppercase">Fully Booked</span>
                                            @else
                                                <span class="inline-block mt-1 text-[10px] text-emerald-600 font-medium">{{ $batch->availableSeats() }} seats left</span>
                                            @endif
                                        </td>

                                        <!-- Timeline -->
                                        <td class="p-4 text-[11px] whitespace-nowrap">
                                            <div class="font-medium text-neutral-800">
                                                {{ $batch->start_date ? $batch->start_date->format('M d, Y') : 'TBD' }}
                                                &ndash;
                                                {{ $batch->end_date ? $batch->end_date->format('M d, Y') : 'TBD' }}
                                            </div>
                                            <div class="text-[10px] text-neutral-400 mt-0.5">Timezone: Asia/Colombo</div>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="p-4 whitespace-nowrap">
                                            @if ($batch->status === 'open_for_enrollment')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Open for Enrollment
                                                </span>
                                            @elseif ($batch->status === 'in_progress')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    In Progress
                                                </span>
                                            @elseif ($batch->status === 'upcoming')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Upcoming
                                                </span>
                                            @elseif ($batch->status === 'completed')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Cancelled
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="p-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-neutral-900 hover:bg-lb-orange text-white font-bold text-xs transition"
                                                    title="Open Batch Management Console"
                                                >
                                                    <i class="fas fa-chalkboard-teacher text-xs" aria-hidden="true"></i>
                                                    <span>Console</span>
                                                </a>

                                                @can('update', $batch)
                                                    <a
                                                        href="{{ route('management.courses.batches.edit', [$course, $batch]) }}"
                                                        class="p-2 rounded-lg text-neutral-500 hover:text-lb-orange hover:bg-orange-50 transition"
                                                        title="Edit Batch"
                                                    >
                                                        <i class="fas fa-edit text-xs" aria-hidden="true"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $batch)
                                                    @if ($batch->enrolled_seats === 0)
                                                        <form method="POST" action="{{ route('management.courses.batches.destroy', [$course, $batch]) }}" onsubmit="return confirm('Delete batch {{ $batch->batch_code }}? This action cannot be undone.');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                                title="Delete Batch"
                                                            >
                                                                <i class="fas fa-trash-alt text-xs" aria-hidden="true"></i>
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
                        <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-base font-bold text-neutral-800">No Physical Batches Configured</h3>
                        <p class="text-xs text-neutral-500 max-w-md mx-auto leading-relaxed">
                            No physical campus batches exist for this course yet. Add a batch to establish student seat limits, Sri Lankan campus venues, weekly timetables, and class attendance registers.
                        </p>
                        @can('create', [\App\Models\CourseBatch::class, $course])
                            <div class="pt-2">
                                <a
                                    href="{{ route('management.courses.batches.create', $course) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                                >
                                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                                    <span>Create First Batch</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
