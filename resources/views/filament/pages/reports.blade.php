<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Revenue -->
            <div class="p-5 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-1">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Gross Platform Revenue</span>
                <div class="text-2xl font-extrabold text-neutral-900">
                    LKR {{ number_format($totalRevenue, 2) }}
                </div>
                <div class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                    <span>LKR {{ number_format($thisMonthRevenue, 2) }}</span>
                    <span class="text-neutral-400 font-normal">this month</span>
                </div>
            </div>

            <!-- Total Enrollments -->
            <div class="p-5 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-1">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Student Enrollments</span>
                <div class="text-2xl font-extrabold text-neutral-900">
                    {{ number_format($totalEnrollments) }}
                </div>
                <div class="text-[11px] text-neutral-500">
                    <strong>{{ number_format($activeEnrollments) }}</strong> active &bull; +{{ $thisMonthEnrollments }} this month
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="p-5 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-1">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Classroom Attendance</span>
                <div class="text-2xl font-extrabold text-neutral-900">
                    {{ $avgAttendanceRate }}%
                </div>
                <div class="text-[11px] text-neutral-500">
                    Across {{ number_format($totalAttendances) }} session check-ins
                </div>
            </div>

            <!-- Certificates & Completion -->
            <div class="p-5 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-1">
                <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider">Credentials Issued</span>
                <div class="text-2xl font-extrabold text-neutral-900">
                    {{ number_format($certificatesIssued) }}
                </div>
                <div class="text-[11px] text-neutral-500">
                    Avg Quiz Score: <strong>{{ $avgQuizScore }}%</strong>
                </div>
            </div>
        </div>

        <!-- Breakdown Grids -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 1. Delivery Mode Breakdown -->
            <div class="p-6 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">
                    Delivery Mode Enrolment Distribution
                </h3>

                <div class="space-y-3">
                    <!-- Online -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-neutral-700">Online Self-Paced</span>
                            <span class="text-neutral-900">{{ $onlineEnrollments }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $totalEnrollments > 0 ? ($onlineEnrollments / $totalEnrollments) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Physical Classroom -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-neutral-700">In-Person Campus Batches</span>
                            <span class="text-neutral-900">{{ $physicalEnrollments }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $totalEnrollments > 0 ? ($physicalEnrollments / $totalEnrollments) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Hybrid -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-neutral-700">Hybrid Cohorts</span>
                            <span class="text-neutral-900">{{ $hybridEnrollments }}</span>
                        </div>
                        <div class="w-full bg-neutral-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-lb-orange h-2.5 rounded-full" style="width: {{ $totalEnrollments > 0 ? ($hybridEnrollments / $totalEnrollments) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Top Enrolled Courses -->
            <div class="p-6 rounded-2xl bg-white border border-neutral-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider">
                    Top Performing Courses
                </h3>

                @if ($topCourses->isNotEmpty())
                    <div class="divide-y divide-neutral-100 text-xs">
                        @foreach ($topCourses as $c)
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="font-medium text-neutral-800 truncate pr-3">
                                    {{ $c->title }}
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-neutral-100 text-neutral-700 font-bold font-mono shrink-0">
                                    {{ $c->enrollments_count }} learners
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-neutral-400 italic">No course enrolments recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
