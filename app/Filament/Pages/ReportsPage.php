<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\QuizAttempt;
use Filament\Pages\Page;

class ReportsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Analytics & System';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Academic & Revenue Reports';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports';

    public function getViewData(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();

        // 1. Course Query scoping
        $courseQuery = Course::query();
        if (! $isAdmin) {
            $courseQuery->whereHas('instructors', fn ($q) => $q->where('users.id', $user->id));
        }
        $courseIds = $courseQuery->pluck('id');

        // 2. Enrollment Data
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $activeEnrollments = Enrollment::whereIn('course_id', $courseIds)->where('status', Enrollment::STATUS_ACTIVE)->count();
        $thisMonthEnrollments = Enrollment::whereIn('course_id', $courseIds)->where('enrolled_at', '>=', now()->startOfMonth())->count();

        // Delivery Mode Breakdown
        $onlineEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->whereHas('deliveryOption', fn ($q) => $q->where('delivery_mode', 'online'))
            ->count();
        $physicalEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->whereHas('deliveryOption', fn ($q) => $q->where('delivery_mode', 'physical'))
            ->count();
        $hybridEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->whereHas('deliveryOption', fn ($q) => $q->where('delivery_mode', 'hybrid'))
            ->count();

        // 3. Financial Revenue in LKR (Admin only or scoped)
        $totalRevenue = $isAdmin
            ? (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)->sum('amount')
            : (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)
                ->whereHas('order.items', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->sum('amount');

        $thisMonthRevenue = $isAdmin
            ? (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)->where('paid_at', '>=', now()->startOfMonth())->sum('amount')
            : (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)
                ->where('paid_at', '>=', now()->startOfMonth())
                ->whereHas('order.items', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->sum('amount');

        // 4. Classroom Attendance Rate
        $attendanceQuery = Attendance::whereHas('session.batch.deliveryOption', fn ($q) => $q->whereIn('course_id', $courseIds));
        $totalAttendances = $attendanceQuery->count();
        $presentAttendances = (clone $attendanceQuery)->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count();
        $avgAttendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 0.0;

        // 5. Academic Certificates & Quiz Performance
        $certificatesIssued = Certificate::whereIn('course_id', $courseIds)->where('is_revoked', false)->count();
        $avgQuizScore = round((float) QuizAttempt::whereHas('quiz', fn ($q) => $q->whereIn('course_id', $courseIds))->avg('score'), 1);

        // Top Courses by Enrollment
        $topCourses = Course::whereIn('id', $courseIds)
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        return [
            'isAdmin' => $isAdmin,
            'totalEnrollments' => $totalEnrollments,
            'activeEnrollments' => $activeEnrollments,
            'thisMonthEnrollments' => $thisMonthEnrollments,
            'onlineEnrollments' => $onlineEnrollments,
            'physicalEnrollments' => $physicalEnrollments,
            'hybridEnrollments' => $hybridEnrollments,
            'totalRevenue' => $totalRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'totalAttendances' => $totalAttendances,
            'avgAttendanceRate' => $avgAttendanceRate,
            'certificatesIssued' => $certificatesIssued,
            'avgQuizScore' => $avgQuizScore,
            'topCourses' => $topCourses,
        ];
    }
}
