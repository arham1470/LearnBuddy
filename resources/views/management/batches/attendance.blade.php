<x-layouts.public title="Attendance Register - {{ $batch->batch_code }} - LearnBuddy" description="Record physical classroom attendance for scheduled class session.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.index', $course) }}" class="hover:text-lb-orange">{{ Str::limit($course->title, 20) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.batches.show', [$course, $batch]) }}" class="hover:text-lb-orange">{{ $batch->batch_code }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Attendance Register</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-clipboard-check text-xs" aria-hidden="true"></i> Classroom Attendance
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        Session Attendance Register
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Batch: <strong>{{ $batch->batch_name }}</strong> ({{ $batch->batch_code }}) &bull; Course: {{ $course->title }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full border border-neutral-300 bg-white text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-sm"
                    >
                        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                        <span>Back to Batch Console</span>
                    </a>
                </div>
            </div>

            <!-- Session Details Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Date &amp; Day</span>
                    <span class="text-xs font-bold text-neutral-900 mt-0.5 block">
                        {{ $session->session_date->format('M d, Y (l)') }}
                    </span>
                    <span class="text-[10px] text-neutral-400">Asia/Colombo</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Scheduled Timing</span>
                    <span class="text-xs font-bold text-neutral-900 mt-0.5 block">
                        {{ $session->formattedTiming() }}
                    </span>
                    <span class="text-[10px] text-neutral-400">Room: {{ $session->room_number ?? 'Main Lab' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Lesson Topic</span>
                    <span class="text-xs font-bold text-neutral-900 mt-0.5 block truncate">
                        {{ $session->topic ?? 'Classroom Session' }}
                    </span>
                    <span class="text-[10px] text-neutral-400">Campus: {{ $batch->location?->city ?? 'N/A' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Session Status</span>
                    <div class="mt-0.5">
                        @if ($session->status === 'completed')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check text-[9px]"></i> Attendance Saved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="fas fa-clock text-[9px]"></i> Pending Record
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance Form -->
            <form method="POST" action="{{ route('management.courses.batches.sessions.attendance.store', [$course, $batch, $session]) }}" class="space-y-6">
                @csrf

                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                    <!-- Quick Actions Toolbar -->
                    <div class="p-4 bg-neutral-50/70 border-b border-neutral-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-xs font-bold text-neutral-700">
                            Enrolled Student Roster ({{ $enrollments->count() }})
                        </div>

                        @if ($enrollments->isNotEmpty())
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    onclick="document.querySelectorAll('input[type=radio][value=present]').forEach(r => r.checked = true)"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold hover:bg-emerald-100 transition cursor-pointer"
                                >
                                    <i class="fas fa-check-double text-[10px] mr-1"></i> Mark All Present
                                </button>
                                <button
                                    type="button"
                                    onclick="document.querySelectorAll('input[type=radio][value=absent]').forEach(r => r.checked = true)"
                                    class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold hover:bg-rose-100 transition cursor-pointer"
                                >
                                    <i class="fas fa-times-circle text-[10px] mr-1"></i> Mark All Absent
                                </button>
                            </div>
                        @endif
                    </div>

                    @if ($enrollments->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                    <tr>
                                        <th class="p-4">Student</th>
                                        <th class="p-4">Attendance Status</th>
                                        <th class="p-4">Remarks / Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                    @foreach ($enrollments as $idx => $enr)
                                        @php
                                            $existingAtt = $attendances->get($enr->user_id);
                                            $currentStatus = old("attendances.{$idx}.status", $existingAtt?->status ?? 'present');
                                            $currentRemarks = old("attendances.{$idx}.remarks", $existingAtt?->remarks);
                                        @endphp
                                        <tr class="hover:bg-neutral-50/50 transition">
                                            <!-- Student Info -->
                                            <td class="p-4 whitespace-nowrap">
                                                <input type="hidden" name="attendances[{{ $idx }}][user_id]" value="{{ $enr->user_id }}">
                                                <div class="font-bold text-neutral-900 text-sm">{{ $enr->user?->name ?? 'Student' }}</div>
                                                <div class="text-[11px] text-neutral-500">{{ $enr->user?->email }}</div>
                                                <div class="text-[10px] font-mono text-neutral-400 mt-0.5">{{ $enr->enrollment_code }}</div>
                                            </td>

                                            <!-- Attendance Radios -->
                                            <td class="p-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <!-- Present -->
                                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer transition {{ $currentStatus === 'present' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-bold' : 'bg-white border-neutral-200 text-neutral-600 hover:bg-neutral-50' }}">
                                                        <input
                                                            type="radio"
                                                            name="attendances[{{ $idx }}][status]"
                                                            value="present"
                                                            class="text-emerald-600 focus:ring-emerald-500"
                                                            {{ $currentStatus === 'present' ? 'checked' : '' }}
                                                            required
                                                        >
                                                        <span>Present</span>
                                                    </label>

                                                    <!-- Late -->
                                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer transition {{ $currentStatus === 'late' ? 'bg-amber-50 border-amber-300 text-amber-800 font-bold' : 'bg-white border-neutral-200 text-neutral-600 hover:bg-neutral-50' }}">
                                                        <input
                                                            type="radio"
                                                            name="attendances[{{ $idx }}][status]"
                                                            value="late"
                                                            class="text-amber-600 focus:ring-amber-500"
                                                            {{ $currentStatus === 'late' ? 'checked' : '' }}
                                                        >
                                                        <span>Late</span>
                                                    </label>

                                                    <!-- Excused -->
                                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer transition {{ $currentStatus === 'excused' ? 'bg-blue-50 border-blue-300 text-blue-800 font-bold' : 'bg-white border-neutral-200 text-neutral-600 hover:bg-neutral-50' }}">
                                                        <input
                                                            type="radio"
                                                            name="attendances[{{ $idx }}][status]"
                                                            value="excused"
                                                            class="text-blue-600 focus:ring-blue-500"
                                                            {{ $currentStatus === 'excused' ? 'checked' : '' }}
                                                        >
                                                        <span>Excused</span>
                                                    </label>

                                                    <!-- Absent -->
                                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border cursor-pointer transition {{ $currentStatus === 'absent' ? 'bg-rose-50 border-rose-300 text-rose-800 font-bold' : 'bg-white border-neutral-200 text-neutral-600 hover:bg-neutral-50' }}">
                                                        <input
                                                            type="radio"
                                                            name="attendances[{{ $idx }}][status]"
                                                            value="absent"
                                                            class="text-rose-600 focus:ring-rose-500"
                                                            {{ $currentStatus === 'absent' ? 'checked' : '' }}
                                                        >
                                                        <span>Absent</span>
                                                    </label>
                                                </div>
                                            </td>

                                            <!-- Remarks -->
                                            <td class="p-4">
                                                <input
                                                    type="text"
                                                    name="attendances[{{ $idx }}][remarks]"
                                                    value="{{ $currentRemarks }}"
                                                    placeholder="Optional note (e.g. arrived 30 mins late with excuse)..."
                                                    class="w-full px-3 py-1.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center text-xs text-neutral-400 space-y-2">
                            <i class="fas fa-users-slash text-2xl text-neutral-300 block"></i>
                            <p class="font-bold text-neutral-800">No Students Enrolled</p>
                            <p class="max-w-sm mx-auto">There are currently no active enrolled students in this physical cohort to record attendance for.</p>
                        </div>
                    @endif
                </div>

                @if ($enrollments->isNotEmpty())
                    <div class="flex items-center justify-between pt-2">
                        <a
                            href="{{ route('management.courses.batches.show', [$course, $batch]) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            <i class="fas fa-save mr-1.5"></i>
                            Save Attendance Register
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </section>
</x-layouts.public>
