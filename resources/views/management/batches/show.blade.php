<x-layouts.public title="Batch Console - {{ $batch->batch_code }} - LearnBuddy" description="Batch management console: timetables, sessions, student enrollments, attendance and audit logs.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 25) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.index', $course) }}" class="hover:text-lb-orange">Batches</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">{{ $batch->batch_code }}</span>
            </div>

            <!-- Flash Notifications -->
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

            <!-- Batch Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-neutral-900 text-white">
                                {{ $batch->batch_code }}
                            </span>

                            @if ($batch->status === 'open_for_enrollment')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Open for Enrollment
                                </span>
                            @elseif ($batch->status === 'in_progress')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    In Progress
                                </span>
                            @elseif ($batch->status === 'upcoming')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Upcoming
                                </span>
                            @elseif ($batch->status === 'completed')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Cancelled
                                </span>
                            @endif

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-lb-orange">
                                {{ ucfirst($batch->deliveryOption?->delivery_mode ?? 'Physical') }} Delivery
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                            {{ $batch->batch_name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-neutral-500">
                            Course: <a href="{{ route('management.courses.show', $course) }}" class="font-semibold text-neutral-700 hover:text-lb-orange underline">{{ $course->title }}</a>
                        </p>
                    </div>

                    <!-- Top Actions -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a
                            href="{{ route('management.courses.batches.index', $course) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-neutral-300 bg-white text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-sm"
                        >
                            <i class="fas fa-list text-xs" aria-hidden="true"></i>
                            <span>All Batches</span>
                        </a>

                        @can('update', $batch)
                            <a
                                href="{{ route('management.courses.batches.edit', [$course, $batch]) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-neutral-900 text-white hover:bg-lb-orange text-xs font-bold transition shadow-sm"
                            >
                                <i class="fas fa-edit text-xs" aria-hidden="true"></i>
                                <span>Edit Batch</span>
                            </a>

                            @if (! $batch->isCancelled())
                                <button
                                    type="button"
                                    onclick="document.getElementById('cancel-batch-dialog').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 text-xs font-bold transition cursor-pointer"
                                >
                                    <i class="fas fa-ban text-xs" aria-hidden="true"></i>
                                    <span>Cancel Batch</span>
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>

                <!-- Meta & Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-neutral-100">
                    <!-- Venue / Location -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1">
                            <i class="fas fa-map-marker-alt text-lb-orange"></i>
                            <span>Campus Venue</span>
                        </div>
                        @if ($batch->location)
                            <div class="font-bold text-neutral-900 text-xs sm:text-sm">{{ $batch->location->name }}</div>
                            <div class="text-[11px] text-neutral-500 mt-0.5">
                                {{ $batch->location->city }}, {{ $batch->location->district }}
                            </div>
                            <div class="text-[10px] text-neutral-400 mt-1">
                                {{ $batch->location->address_line_1 }}
                            </div>
                        @else
                            <span class="text-xs text-neutral-400 italic">None assigned</span>
                        @endif
                    </div>

                    <!-- Seat Capacity -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-users text-blue-500"></i>
                                <span>Capacity</span>
                            </span>
                            <span class="{{ $batch->isFull() ? 'text-rose-600 font-bold' : 'text-emerald-600' }}">
                                {{ $batch->enrolled_seats }} / {{ $batch->max_seats }}
                            </span>
                        </div>
                        @php
                            $pct = $batch->max_seats > 0 ? min(100, round(($batch->enrolled_seats / $batch->max_seats) * 100)) : 0;
                        @endphp
                        <div class="w-full bg-neutral-200 rounded-full h-2 my-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $batch->isFull() ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-neutral-500">
                            <span>{{ $batch->availableSeats() }} seats available</span>
                            <span>{{ $pct }}% booked</span>
                        </div>
                    </div>

                    <!-- Batch Dates -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1">
                            <i class="fas fa-calendar-alt text-amber-500"></i>
                            <span>Schedule Range</span>
                        </div>
                        <div class="text-xs font-bold text-neutral-900">
                            {{ $batch->start_date ? $batch->start_date->format('M d, Y') : 'TBD' }}
                            &ndash;
                            {{ $batch->end_date ? $batch->end_date->format('M d, Y') : 'TBD' }}
                        </div>
                        <div class="text-[11px] text-neutral-500 mt-1">
                            Timezone: <strong>Asia/Colombo</strong>
                        </div>
                    </div>

                    <!-- Enrolled Students & Sessions -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/70">
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1">
                            <i class="fas fa-chart-pie text-purple-500"></i>
                            <span>Activity Counts</span>
                        </div>
                        <div class="text-xs font-semibold text-neutral-800 space-y-0.5">
                            <div><strong>{{ $batch->activeEnrollments->count() }}</strong> active enrolled student(s)</div>
                            <div><strong>{{ $batch->sessions->count() }}</strong> classroom session(s) scheduled</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancel Batch Modal / Dialog -->
            <div id="cancel-batch-dialog" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl">
                    <div class="flex items-center gap-3 text-rose-600">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-lg">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-900">Cancel Batch {{ $batch->batch_code }}</h3>
                            <p class="text-xs text-neutral-500">This will update the batch status to Cancelled and record an audit entry.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('management.courses.batches.cancel', [$course, $batch]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="cancel_reason" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Cancellation Reason <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                id="cancel_reason"
                                name="reason"
                                rows="3"
                                required
                                placeholder="Explain why this physical cohort is being cancelled..."
                                class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button
                                type="button"
                                onclick="document.getElementById('cancel-batch-dialog').classList.add('hidden')"
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

            <!-- SECTION 1 & 2: TIMETABLE & CLASS SESSIONS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- 1. Weekly Recurring Timetable -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                        <div>
                            <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-clock text-amber-500"></i>
                                <span>Weekly Recurring Timetable</span>
                            </h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Regular weekly time slots for this batch.</p>
                        </div>
                        <span class="text-xs font-mono font-bold bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded">
                            {{ $batch->schedules->count() }} slot(s)
                        </span>
                    </div>

                    <!-- Slot List -->
                    @if ($batch->schedules->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($batch->schedules as $slot)
                                <div class="p-3.5 rounded-2xl bg-neutral-50/80 border border-neutral-200/80 flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($slot->day_of_week, 0, 3) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-neutral-900">
                                                {{ $slot->day_of_week }} &bull;
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', strlen($slot->start_time) === 5 ? $slot->start_time.':00' : $slot->start_time)->format('h:i A') }}
                                                &ndash;
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', strlen($slot->end_time) === 5 ? $slot->end_time.':00' : $slot->end_time)->format('h:i A') }}
                                            </div>
                                            <div class="text-[11px] text-neutral-500 mt-0.5">
                                                Room: <strong>{{ $slot->room_number ?? 'Main Classroom' }}</strong>
                                                @if ($slot->notes)
                                                    &bull; <span class="italic">{{ $slot->notes }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @can('update', $batch)
                                        <button
                                            type="button"
                                            data-item="{{ $slot->day_of_week }} (Room: {{ $slot->room_number ?? 'Main Classroom' }})"
                                            data-action="{{ route('management.courses.batches.schedules.destroy', [$course, $batch, $slot]) }}"
                                            onclick="confirmDialog({
                                                title: 'Remove Schedule Slot?',
                                                itemName: this.dataset.item,
                                                message: 'Are you sure you want to remove this weekly recurring classroom schedule slot?',
                                                action: this.dataset.action,
                                                method: 'DELETE',
                                                confirmText: 'Remove Slot',
                                                confirmVariant: 'danger'
                                            })"
                                            class="w-7 h-7 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer shrink-0"
                                            title="Delete Slot"
                                        >
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-neutral-400 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200">
                            <i class="fas fa-calendar-plus text-xl text-neutral-300 mb-1 block"></i>
                            No recurring weekly slots added yet. Use the form below to configure day and times.
                        </div>
                    @endif

                    <!-- Add Weekly Slot Form -->
                    @can('update', $batch)
                        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/60 space-y-3">
                            <h3 class="text-xs font-bold text-neutral-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-plus text-lb-orange"></i>
                                <span>Add Weekly Schedule Slot</span>
                            </h3>

                            <form method="POST" action="{{ route('management.courses.batches.schedules.store', [$course, $batch]) }}" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Day of Week</label>
                                        <select
                                            name="day_of_week"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                            required
                                        >
                                            <option value="Saturday">Saturday</option>
                                            <option value="Sunday">Sunday</option>
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Start Time (24h)</label>
                                        <input
                                            type="time"
                                            name="start_time"
                                            value="09:00"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">End Time (24h)</label>
                                        <input
                                            type="time"
                                            name="end_time"
                                            value="13:00"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Room / Lab No.</label>
                                        <input
                                            type="text"
                                            name="room_number"
                                            placeholder="e.g. Lab 02, Room 301"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-neutral-600 uppercase mb-1">Notes</label>
                                        <input
                                            type="text"
                                            name="notes"
                                            placeholder="e.g. Bring your own laptop"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-neutral-300 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                        >
                                    </div>
                                </div>

                                <div class="text-right pt-1">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs transition shadow-sm cursor-pointer"
                                    >
                                        Save Schedule Slot
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endcan
                </div>

                <!-- 2. Schedule Specific Class Session -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                        <div>
                            <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-calendar-check text-emerald-600"></i>
                                <span>Schedule Class Session</span>
                            </h2>
                            <p class="text-xs text-neutral-500 mt-0.5">Generate a class calendar session for attendance tracking.</p>
                        </div>
                    </div>

                    @can('recordAttendance', $batch)
                        <form method="POST" action="{{ route('management.courses.batches.sessions.store', [$course, $batch]) }}" class="space-y-3.5">
                            @csrf
                            <div>
                                <label for="session_date" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1">
                                    Session Date (Asia/Colombo) <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="session_date"
                                    name="session_date"
                                    value="{{ now()->timezone('Asia/Colombo')->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1">
                                        Start Time <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        name="start_time"
                                        value="09:00"
                                        class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1">
                                        End Time <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="time"
                                        name="end_time"
                                        value="13:00"
                                        class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                        required
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="topic" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1">
                                    Session Lesson Topic / Agenda
                                </label>
                                <input
                                    type="text"
                                    id="topic"
                                    name="topic"
                                    placeholder="e.g. Module 3: Advanced SQL Joins & Query Optimization Lab"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>

                            <div>
                                <label for="room_number" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1">
                                    Classroom / Lab Number
                                </label>
                                <input
                                    type="text"
                                    id="room_number"
                                    name="room_number"
                                    placeholder="e.g. Lab 4B (Colombo Campus)"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-neutral-50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>

                            <div class="pt-2 text-right">
                                <button
                                    type="submit"
                                    class="px-5 py-2.5 rounded-full bg-neutral-900 hover:bg-lb-orange text-white font-bold text-xs transition shadow-md cursor-pointer"
                                >
                                    <i class="fas fa-plus text-xs mr-1"></i>
                                    Schedule Class Session
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>

            <!-- CLASS SESSIONS & ATTENDANCE REGISTER TABLE -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-user-check text-blue-600"></i>
                            <span>Conducted &amp; Scheduled Class Sessions</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Track class dates, session topics, and record student attendance registers.</p>
                    </div>
                    <span class="text-xs font-mono font-bold bg-neutral-100 text-neutral-700 px-2.5 py-1 rounded-full">
                        {{ $batch->sessions->count() }} Session(s)
                    </span>
                </div>

                @if ($batch->sessions->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-3.5">Session Date</th>
                                    <th class="p-3.5">Time &amp; Venue</th>
                                    <th class="p-3.5">Topic / Agenda</th>
                                    <th class="p-3.5">Conducted By</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5 text-right">Attendance Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($batch->sessions as $ses)
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <td class="p-3.5 whitespace-nowrap font-bold text-neutral-900">
                                            {{ $ses->session_date->format('M d, Y (D)') }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <div class="font-medium text-neutral-800">
                                                {{ $ses->formattedTiming() }}
                                            </div>
                                            <div class="text-[11px] text-neutral-400">
                                                Room: {{ $ses->room_number ?? 'Main Hall' }}
                                            </div>
                                        </td>
                                        <td class="p-3.5">
                                            <span class="font-medium text-neutral-900">{{ $ses->topic ?? 'Standard Lecture & Lab' }}</span>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap text-neutral-600">
                                            {{ $ses->conductedBy?->name ?? 'Instructor' }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            @if ($ses->status === 'completed')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-check text-[9px]"></i> Completed
                                                </span>
                                            @elseif ($ses->status === 'scheduled')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <i class="fas fa-clock text-[9px]"></i> Scheduled
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <i class="fas fa-times text-[9px]"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right whitespace-nowrap">
                                            @can('recordAttendance', $batch)
                                                <a
                                                    href="{{ route('management.courses.batches.sessions.attendance', [$course, $batch, $ses]) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-lb-orange hover:bg-orange-600 text-white font-bold text-xs transition shadow-sm"
                                                >
                                                    <i class="fas fa-clipboard-check text-xs"></i>
                                                    <span>{{ $ses->status === 'completed' ? 'View / Edit Register' : 'Record Attendance' }}</span>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-neutral-400 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200">
                        <i class="fas fa-calendar-times text-2xl text-neutral-300 mb-1 block"></i>
                        No class sessions scheduled yet. Use the form above to schedule upcoming calendar sessions.
                    </div>
                @endif
            </div>

            <!-- SECTION 3: ENROLLED STUDENTS ROSTER -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-lb-orange"></i>
                            <span>Enrolled Students Roster</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Students with confirmed seats in this batch and their attendance rates.</p>
                    </div>
                    <span class="text-xs font-mono font-bold bg-neutral-100 text-neutral-700 px-2.5 py-1 rounded-full">
                        {{ $batch->activeEnrollments->count() }} Student(s)
                    </span>
                </div>

                @if ($batch->activeEnrollments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-3.5">Student</th>
                                    <th class="p-3.5">Contact Details</th>
                                    <th class="p-3.5">Enrolled Date</th>
                                    <th class="p-3.5">Delivery Status</th>
                                    <th class="p-3.5 text-right">Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($batch->activeEnrollments as $enr)
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <td class="p-3.5 whitespace-nowrap">
                                            <div class="font-bold text-neutral-900">{{ $enr->user?->name ?? 'Student' }}</div>
                                            <div class="text-[11px] text-neutral-400 font-mono">{{ $enr->enrollment_code }}</div>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <div class="text-neutral-700">{{ $enr->user?->email }}</div>
                                            <div class="text-[11px] text-neutral-400">{{ $enr->user?->phone_number ?? 'No phone' }}</div>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap text-neutral-600">
                                            {{ $enr->enrolled_at ? $enr->enrolled_at->format('M d, Y') : 'Active' }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ ucfirst($enr->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 text-right whitespace-nowrap">
                                            @php
                                                $rate = $enr->attendanceRate();
                                            @endphp
                                            <span class="font-bold text-xs {{ $rate >= 80 ? 'text-emerald-600' : ($rate >= 50 ? 'text-amber-600' : 'text-neutral-500') }}">
                                                {{ number_format($rate, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-neutral-400 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200">
                        <i class="fas fa-users-slash text-2xl text-neutral-300 mb-1 block"></i>
                        No students enrolled in this batch yet.
                    </div>
                @endif
            </div>

            <!-- SECTION 4: AUDIT TRAIL / SCHEDULE CHANGES -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-history text-neutral-500"></i>
                            <span>Schedule &amp; Venue Change Audit Log</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Audit log of updates, cancellations, and timetable adjustments.</p>
                    </div>
                </div>

                @if ($batch->scheduleChanges->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($batch->scheduleChanges as $log)
                            <div class="p-3.5 rounded-2xl bg-neutral-50 border border-neutral-200/70 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-neutral-900 uppercase font-mono text-[10px] bg-neutral-200 px-2 py-0.5 rounded">
                                            {{ str_replace('_', ' ', $log->change_type) }}
                                        </span>
                                        <span class="text-neutral-600">by <strong>{{ $log->changedBy?->name ?? 'System' }}</strong></span>
                                    </div>
                                    <span class="text-[11px] text-neutral-400">
                                        {{ $log->created_at->timezone('Asia/Colombo')->format('M d, Y h:i A') }} (Asia/Colombo)
                                    </span>
                                </div>
                                @if ($log->reason)
                                    <p class="text-neutral-700 italic pt-1">
                                        &ldquo;{{ $log->reason }}&rdquo;
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-xs text-neutral-400 italic">
                        No schedule adjustments or cancellations have been recorded for this batch.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
