<x-layouts.public title="{{ $batch->batch_name }} - My Campus Batch - LearnBuddy" description="View your physical campus schedule, classroom venue, and personal attendance history.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('student.dashboard') }}" class="hover:text-lb-orange">Student Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">{{ $batch->batch_name }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold font-mono bg-neutral-900 text-white">
                            {{ $batch->batch_code }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            Physical Campus Intake
                        </span>
                        @if ($batch->status === 'in_progress')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                In Progress
                            </span>
                        @elseif ($batch->status === 'upcoming')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Upcoming
                            </span>
                        @elseif ($batch->status === 'completed')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                Completed
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        {{ $batch->batch_name }}
                    </h1>
                    @if ($batch->deliveryOption?->course)
                        <p class="text-xs sm:text-sm text-neutral-600">
                            Course: <strong>{{ $batch->deliveryOption->course->title }}</strong>
                        </p>
                    @endif
                </div>

                <!-- Learning Portal Shortcut -->
                @if ($batch->deliveryOption?->course)
                    <div class="shrink-0">
                        <a
                            href="{{ route('learning.index', $batch->deliveryOption->course->slug) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-play-circle text-xs" aria-hidden="true"></i>
                            <span>Access Learning Portal</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Campus & Attendance Rate Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 1. Campus Location -->
                <div class="md:col-span-2 bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-neutral-400 uppercase tracking-wider">
                        <i class="fas fa-map-marker-alt text-lb-orange"></i>
                        <span>Campus Training Centre</span>
                    </div>

                    @if ($batch->location)
                        <h2 class="text-base sm:text-lg font-bold text-neutral-900">
                            {{ $batch->location->name }}
                        </h2>
                        <div class="text-xs text-neutral-600 space-y-1">
                            <p class="font-medium text-neutral-800">{{ $batch->location->address_line_1 }}</p>
                            @if ($batch->location->address_line_2)
                                <p>{{ $batch->location->address_line_2 }}</p>
                            @endif
                            <p class="text-neutral-500">
                                {{ $batch->location->city }}, {{ $batch->location->district }} &bull; {{ $batch->location->province }} Province
                                @if ($batch->location->postal_code)
                                    ({{ $batch->location->postal_code }})
                                @endif
                            </p>
                        </div>

                        @if ($batch->location->contact_phone)
                            <div class="pt-2 border-t border-neutral-100 flex items-center gap-2 text-xs text-neutral-600">
                                <i class="fas fa-phone-alt text-neutral-400"></i>
                                <span>Campus Enquiries: <strong>{{ $batch->location->contact_phone }}</strong></span>
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-neutral-500 italic">No physical campus assigned yet.</p>
                    @endif
                </div>

                <!-- 2. Personal Attendance Rate -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-400 uppercase tracking-wider">
                            <i class="fas fa-clipboard-check text-emerald-600"></i>
                            <span>My Attendance</span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-neutral-900">
                                {{ number_format($attendanceRate, 1) }}%
                            </span>
                            <span class="text-xs font-semibold {{ $attendanceRate >= 80 ? 'text-emerald-600' : ($attendanceRate >= 50 ? 'text-amber-600' : 'text-neutral-400') }}">
                                {{ $attendanceRate >= 80 ? 'Good Standing' : ($attendanceRate >= 50 ? 'Warning' : 'Low') }}
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-neutral-500 pt-3 border-t border-neutral-100">
                        @php
                            $presentCount = $myAttendances->where('status', 'present')->count();
                            $lateCount = $myAttendances->where('status', 'late')->count();
                            $absentCount = $myAttendances->where('status', 'absent')->count();
                        @endphp
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2 rounded-xl bg-emerald-50 text-emerald-800 font-bold">
                                <div>{{ $presentCount }}</div>
                                <div class="text-[10px] font-normal text-emerald-600">Present</div>
                            </div>
                            <div class="p-2 rounded-xl bg-amber-50 text-amber-800 font-bold">
                                <div>{{ $lateCount }}</div>
                                <div class="text-[10px] font-normal text-amber-600">Late</div>
                            </div>
                            <div class="p-2 rounded-xl bg-rose-50 text-rose-800 font-bold">
                                <div>{{ $absentCount }}</div>
                                <div class="text-[10px] font-normal text-rose-600">Absent</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recurring Weekly Timetable -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-calendar-week text-amber-500"></i>
                            <span>Weekly Recurring Timetable</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Recurring weekly physical class schedule (Asia/Colombo).</p>
                    </div>
                </div>

                @if ($batch->schedules->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($batch->schedules as $slot)
                            <div class="p-4 rounded-2xl bg-neutral-50/80 border border-neutral-200/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-neutral-900">{{ $slot->day_of_week }}</span>
                                    <span class="text-[10px] font-mono font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded">
                                        {{ $slot->room_number ?? 'Lab Hall' }}
                                    </span>
                                </div>
                                <div class="text-xs font-semibold text-neutral-700">
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', strlen($slot->start_time) === 5 ? $slot->start_time.':00' : $slot->start_time)->format('h:i A') }}
                                    &ndash;
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', strlen($slot->end_time) === 5 ? $slot->end_time.':00' : $slot->end_time)->format('h:i A') }}
                                </div>
                                @if ($slot->notes)
                                    <p class="text-[11px] text-neutral-500 italic">{{ $slot->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-neutral-400 italic">No recurring timetable slots published for this batch yet.</p>
                @endif
            </div>

            <!-- Sessions & Attendance Timeline -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-history text-blue-600"></i>
                            <span>Class Calendar &amp; My Attendance Record</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Your personal attendance status recorded by instructors for each session.</p>
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
                                    <th class="p-3.5">Scheduled Time</th>
                                    <th class="p-3.5">Lesson Topic / Lab</th>
                                    <th class="p-3.5">Room</th>
                                    <th class="p-3.5 text-right">My Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($batch->sessions as $ses)
                                    @php
                                        $att = $myAttendances->get($ses->id);
                                    @endphp
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <td class="p-3.5 whitespace-nowrap font-bold text-neutral-900">
                                            {{ $ses->session_date->format('M d, Y (l)') }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap text-neutral-700">
                                            {{ $ses->formattedTiming() }}
                                        </td>
                                        <td class="p-3.5 font-medium text-neutral-900">
                                            {{ $ses->topic ?? 'Classroom Lecture & Practice' }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap text-neutral-500">
                                            {{ $ses->room_number ?? 'Main Classroom' }}
                                        </td>
                                        <td class="p-3.5 text-right whitespace-nowrap">
                                            @if ($att)
                                                @if ($att->status === 'present')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fas fa-check text-[9px]"></i> Present
                                                    </span>
                                                @elseif ($att->status === 'late')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <i class="fas fa-clock text-[9px]"></i> Late
                                                    </span>
                                                @elseif ($att->status === 'excused')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                        <i class="fas fa-info-circle text-[9px]"></i> Excused
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <i class="fas fa-times text-[9px]"></i> Absent
                                                    </span>
                                                @endif
                                                @if ($att->remarks)
                                                    <div class="text-[10px] text-neutral-400 italic mt-0.5">{{ $att->remarks }}</div>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-neutral-100 text-neutral-600">
                                                    <i class="fas fa-calendar text-[9px]"></i> Scheduled
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-neutral-400 bg-neutral-50 rounded-2xl border border-dashed border-neutral-200">
                        <i class="fas fa-calendar-times text-2xl text-neutral-300 mb-1 block"></i>
                        No specific class dates scheduled yet. Your instructor will publish session dates and lab timings soon.
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
