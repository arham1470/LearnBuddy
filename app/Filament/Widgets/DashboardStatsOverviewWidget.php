<?php

namespace App\Filament\Widgets;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\Widget;

class DashboardStatsOverviewWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.dashboard-stats-overview';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->isAdmin();

        // Scope queries for instructors
        $courseQuery = Course::query();
        if (! $isAdmin) {
            $courseQuery->whereHas('instructors', fn ($q) => $q->where('users.id', $user->id));
        }
        $courseIds = $courseQuery->pluck('id');

        $activeCourses = (clone $courseQuery)->where('status', Course::STATUS_PUBLISHED)->count();

        $enrolledStudents = $isAdmin
            ? User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count()
            : Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');

        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();

        $totalRevenue = $isAdmin
            ? (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)->sum('amount')
            : (float) Payment::where('status', Payment::STATUS_SUCCESSFUL)
                ->whereHas('order.items', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->sum('amount');

        $attendanceQuery = Attendance::whereHas('session.batch.deliveryOption', fn ($q) => $q->whereIn('course_id', $courseIds));
        $totalAtt = $attendanceQuery->count();
        $presentAtt = (clone $attendanceQuery)->whereIn('status', [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE])->count();
        $attendanceRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 0;

        $pendingGrading = AssignmentSubmission::where('status', 'submitted')
            ->whereHas('assignment', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->count();

        return [
            'stats' => [
                [
                    'label'       => 'Active Courses',
                    'value'       => number_format($activeCourses),
                    'description' => 'Published courses',
                    'icon'        => 'academic-cap',
                    'featured'    => true,
                ],
                [
                    'label'       => 'Total Learners',
                    'value'       => number_format($enrolledStudents),
                    'description' => 'Enrolled Sri Lankan students',
                    'icon'        => 'user-group',
                    'featured'    => false,
                ],
                [
                    'label'       => 'Enrollments',
                    'value'       => number_format($totalEnrollments),
                    'description' => 'Total course seats',
                    'icon'        => 'rectangle-stack',
                    'featured'    => false,
                ],
                [
                    'label'       => 'Total Revenue',
                    'value'       => 'LKR ' . number_format($totalRevenue, 2),
                    'description' => $isAdmin ? 'All settled payments' : 'Your instructed courses',
                    'icon'        => 'banknotes',
                    'featured'    => false,
                ],
                [
                    'label'       => 'Campus Attendance',
                    'value'       => $attendanceRate . '%',
                    'description' => 'Physical check-in rate',
                    'icon'        => 'clipboard-document-check',
                    'featured'    => false,
                ],
                [
                    'label'       => 'Pending Grading',
                    'value'       => number_format($pendingGrading),
                    'description' => 'Submissions needing review',
                    'icon'        => 'pencil-square',
                    'featured'    => false,
                ],
            ],
        ];
    }
}
