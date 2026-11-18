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
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden" x-data="{ expandedBatch: null }">
                @if ($batches->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 bg-neutral-50/80 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                                <th class="py-3 px-4 sm:px-6">Batch Intake</th>
                                <th class="py-3 px-4">Campus Location</th>
                                <th class="py-3 px-4">Enrolment</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right pr-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($batches as $batch)
                                <tr class="hover:bg-neutral-50/60 transition cursor-pointer select-none"
                                    :class="{ 'bg-orange-50/30': expandedBatch === {{ $batch->id }} }"
                                    @click="if (!$event.target.closest('a, button, input')) { expandedBatch = (expandedBatch === {{ $batch->id }} ? null : {{ $batch->id }}); }">

                                    <!-- 1. Batch & Intake (Single Line) -->
                                    <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2.5">
                                            <button
                                                type="button"
                                                @click.stop="expandedBatch = (expandedBatch === {{ $batch->id }} ? null : {{ $batch->id }})"
                                                class="w-6 h-6 rounded-lg bg-neutral-100 hover:bg-orange-100 text-neutral-400 hover:text-lb-orange flex items-center justify-center transition shrink-0"
                                                title="Toggle batch details"
                                            >
                                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="{ 'rotate-180 text-lb-orange': expandedBatch === {{ $batch->id }} }"></i>
                                            </button>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-bold bg-neutral-100 text-neutral-800 shrink-0">
                                                {{ $batch->batch_code }}
                                            </span>
                                            <span class="font-bold text-neutral-900 text-sm hover:text-lb-orange transition truncate max-w-[200px] sm:max-w-xs">
                                                {{ $batch->batch_name }}
                                            </span>
                                            <span class="hidden lg:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $batch->deliveryOption?->delivery_mode === 'hybrid' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($batch->deliveryOption?->delivery_mode ?? 'Physical') }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 2. Campus Location (Single Line) -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                        @if ($batch->location)
                                            <span class="font-bold text-neutral-800">{{ $batch->location->name }}</span>
                                            <span class="text-neutral-400 text-[11px] ml-1">· {{ $batch->location->city }}</span>
                                        @else
                                            <span class="text-neutral-400 italic">No campus</span>
                                        @endif
                                    </td>

                                    <!-- 3. Enrolment (Single Line) -->
                                    <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                        @php
                                            $pct = $batch->max_seats > 0 ? min(100, round(($batch->enrolled_seats / $batch->max_seats) * 100)) : 0;
                                        @endphp
                                        <span class="font-extrabold text-neutral-900">{{ $batch->enrolled_seats }}/{{ $batch->max_seats }}</span>
                                        <span class="text-[11px] font-semibold ml-1 {{ $batch->isFull() ? 'text-rose-600' : ($pct > 75 ? 'text-amber-600' : 'text-emerald-600') }}">
                                            ({{ $batch->isFull() ? 'Full' : $batch->availableSeats().' left' }})
                                        </span>
                                    </td>

                                    <!-- 4. Status (Single Line) -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if ($batch->status === 'open_for_enrollment')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Open
                                            </span>
                                        @elseif ($batch->status === 'in_progress')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                In Progress
                                            </span>
                                        @elseif ($batch->status === 'upcoming')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Upcoming
                                            </span>
                                        @elseif ($batch->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>

                                    <!-- 5. Actions (Single Line) -->
                                    <td class="py-3.5 px-4 text-right pr-6 whitespace-nowrap">
                                        <!-- Details Toggle Pill -->
                                        <button
                                            type="button"
                                            @click.stop="expandedBatch = (expandedBatch === {{ $batch->id }} ? null : {{ $batch->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold text-xs transition cursor-pointer border shadow-2xs"
                                            :class="expandedBatch === {{ $batch->id }} ? 'bg-[#FF5F00] text-white border-[#FF5F00] shadow-orange-500/20' : 'bg-neutral-50 hover:bg-neutral-100 text-neutral-800 border-neutral-200'"
                                            title="View batch operations & details"
                                        >
                                            <span>Details</span>
                                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="{ 'rotate-180': expandedBatch === {{ $batch->id }} }"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expandable Batch Operations & Detail Hub Row -->
                                <tr x-show="expandedBatch === {{ $batch->id }}" x-cloak class="bg-gradient-to-b from-[#FFFDF9] to-[#FFF9F5] border-b border-orange-100">
                                    <td colspan="5" class="p-4 sm:p-6 whitespace-normal">
                                        <div class="bg-white rounded-2xl border border-orange-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                                            <!-- Panel Header: Breadcrumb & Quick Actions -->
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-neutral-100 pb-3">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-lb-orange"></span>
                                                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Campus Batch Operations</span>
                                                    <span class="text-xs text-neutral-300">•</span>
                                                    <span class="text-xs font-semibold text-neutral-800 font-mono">{{ $batch->batch_code }}</span>
                                                    <span class="text-xs text-neutral-400">({{ $batch->batch_name }})</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-900 hover:bg-lb-orange text-white text-xs font-bold transition shadow-xs">
                                                        <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                                                        <span>Full Batch Console</span>
                                                    </a>
                                                    @can('update', $batch)
                                                        <a href="{{ route('management.courses.batches.edit', [$course, $batch]) }}"
                                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-lb-orange text-xs font-bold transition border border-orange-200/60">
                                                            <i class="fas fa-sliders-h text-[10px]"></i>
                                                            <span>Batch Settings</span>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $batch)
                                                        @if ($batch->enrolled_seats === 0)
                                                            <button
                                                                type="button"
                                                                data-item="{{ $batch->batch_code }}"
                                                                data-action="{{ route('management.courses.batches.destroy', [$course, $batch]) }}"
                                                                onclick="confirmDialog({
                                                                    title: 'Delete Physical Batch',
                                                                    itemName: this.dataset.item,
                                                                    message: 'Are you sure you want to permanently delete this physical classroom batch? This action cannot be undone.',
                                                                    action: this.dataset.action,
                                                                    method: 'DELETE',
                                                                    confirmText: 'Delete Batch',
                                                                    confirmVariant: 'danger'
                                                                })"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 text-xs font-bold transition border border-transparent hover:border-rose-200 cursor-pointer"
                                                                title="Delete Batch"
                                                            >
                                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                                                <span>Delete</span>
                                                            </button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </div>

                                            <!-- 4 Operation Hub Cards Grid -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                                                <!-- 1. Weekly Routine & Timetable -->
                                                <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                                                   class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/50 hover:bg-white hover:border-neutral-900 hover:shadow-xs transition group flex flex-col justify-between">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="w-9 h-9 rounded-xl bg-neutral-900 text-white flex items-center justify-center text-sm shadow-xs group-hover:scale-105 transition-transform">
                                                            <i class="fas fa-clock"></i>
                                                        </div>
                                                        <i class="fas fa-arrow-right text-[10px] text-neutral-400 group-hover:text-neutral-900 group-hover:translate-x-0.5 transition-all"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-sm text-neutral-900">Weekly Routine</h4>
                                                        <p class="text-[11px] text-neutral-500 mt-0.5">Recurring timetable schedule</p>
                                                        <div class="mt-2.5 space-y-1">
                                                            @forelse ($batch->schedules->take(2) as $slot)
                                                                <div class="text-[11px] font-semibold text-neutral-800 flex items-center gap-1 truncate">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                                    <span>{{ substr($slot->day_of_week, 0, 3) }}: {{ \Carbon\Carbon::createFromFormat('H:i:s', strlen($slot->start_time) === 5 ? $slot->start_time.':00' : $slot->start_time)->format('h:i A') }}</span>
                                                                    <span class="text-neutral-400">({{ $slot->room_number ?? 'Lab' }})</span>
                                                                </div>
                                                            @empty
                                                                <div class="text-[11px] text-neutral-400 italic">No recurring slots set</div>
                                                            @endforelse
                                                            @if ($batch->schedules->count() > 2)
                                                                <div class="text-[10px] font-bold text-lb-orange">+{{ $batch->schedules->count() - 2 }} more weekly slots</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>

                                                <!-- 2. Class Sessions & Attendance -->
                                                <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                                                   class="p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/40 hover:bg-white hover:border-emerald-600 hover:shadow-xs transition group flex flex-col justify-between">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm shadow-xs group-hover:scale-105 transition-transform">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </div>
                                                        <i class="fas fa-arrow-right text-[10px] text-emerald-500 group-hover:translate-x-0.5 transition-all"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-sm text-neutral-900 group-hover:text-emerald-700 transition-colors">Class Sessions</h4>
                                                        <p class="text-[11px] text-neutral-500 mt-0.5">Calendar dates &amp; registers</p>
                                                        <div class="mt-2.5 text-[11px] font-bold text-emerald-700 flex items-center gap-1.5">
                                                            <span>{{ $batch->sessions_count }} Dated Sessions</span>
                                                        </div>
                                                        <div class="text-[10px] text-neutral-500 mt-0.5">Take attendance &amp; agendas</div>
                                                    </div>
                                                </a>

                                                <!-- 3. Enrolled Students & Visual Capacity -->
                                                <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                                                   class="p-4 rounded-xl border border-blue-200/80 bg-blue-50/40 hover:bg-white hover:border-blue-600 hover:shadow-xs transition group flex flex-col justify-between">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-sm shadow-xs group-hover:scale-105 transition-transform">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                        <i class="fas fa-arrow-right text-[10px] text-blue-500 group-hover:translate-x-0.5 transition-all"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-sm text-neutral-900 group-hover:text-blue-700 transition-colors">Enrolled Students</h4>
                                                        <p class="text-[11px] text-neutral-500 mt-0.5">Seat allocation &amp; roster</p>
                                                        <div class="mt-2 text-xs font-bold text-blue-700 flex justify-between items-center">
                                                            <span>{{ $batch->enrolled_seats }} / {{ $batch->max_seats }}</span>
                                                            <span>{{ $pct }}%</span>
                                                        </div>
                                                        <div class="w-full bg-blue-200/60 rounded-full h-1.5 mt-1 overflow-hidden">
                                                            <div class="h-1.5 rounded-full {{ $batch->isFull() ? 'bg-rose-500' : ($pct > 75 ? 'bg-amber-500' : 'bg-blue-600') }}" style="width: {{ $pct }}%"></div>
                                                        </div>
                                                        <div class="text-[10px] text-neutral-500 mt-1">
                                                            {{ $batch->availableSeats() }} seats left · {{ $batch->active_enrollments_count }} active
                                                        </div>
                                                    </div>
                                                </a>

                                                <!-- 4. Campus Logistics & Venue -->
                                                <div class="p-4 rounded-xl border border-amber-200/80 bg-amber-50/40 flex flex-col justify-between">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm shadow-xs">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </div>
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-200/60 text-amber-900">Venue</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-sm text-neutral-900 truncate">{{ $batch->location?->name ?? 'Campus Location' }}</h4>
                                                        <p class="text-[11px] text-neutral-600 mt-0.5 line-clamp-1">
                                                            {{ $batch->location?->address_line_1 ?? 'Address not specified' }}, {{ $batch->location?->city ?? '' }}
                                                        </p>
                                                        <div class="mt-2 text-[10px] text-neutral-500 space-y-0.5">
                                                            <div>Room capacity: <strong>{{ $batch->location?->max_room_capacity ?? 30 }} seats</strong></div>
                                                            @if ($batch->location?->contact_phone)
                                                                <div>Phone: {{ $batch->location->contact_phone }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Panel Footer: Key Metadata & Timelines -->
                                            <div class="pt-3 border-t border-neutral-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs text-neutral-600">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-calendar-alt text-lb-orange"></i>
                                                    <div>
                                                        <span class="text-neutral-400 text-[10px] uppercase font-bold block">Registration Window</span>
                                                        <span class="font-semibold text-neutral-800">
                                                            {{ $batch->registration_start_date ? $batch->registration_start_date->format('M d, Y') : 'Open' }}
                                                            &ndash;
                                                            {{ $batch->registration_end_date ? $batch->registration_end_date->format('M d, Y') : 'Until Start' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-hourglass-half text-amber-500"></i>
                                                    <div>
                                                        <span class="text-neutral-400 text-[10px] uppercase font-bold block">Class Timetable Duration</span>
                                                        <span class="font-semibold text-neutral-800">
                                                            {{ $batch->start_date ? $batch->start_date->format('M d, Y') : 'TBD' }}
                                                            &ndash;
                                                            {{ $batch->end_date ? $batch->end_date->format('M d, Y') : 'TBD' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-tag text-emerald-500"></i>
                                                    <div>
                                                        <span class="text-neutral-400 text-[10px] uppercase font-bold block">Delivery Mode &amp; Fee</span>
                                                        <span class="font-semibold text-neutral-800">
                                                            {{ $batch->deliveryOption?->title ?? 'Physical Intake' }}
                                                            @if ($batch->deliveryOption)
                                                                <span class="text-neutral-500 font-normal">({{ $batch->deliveryOption->formattedPrice() }})</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
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
