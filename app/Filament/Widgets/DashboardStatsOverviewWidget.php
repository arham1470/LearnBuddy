<?php

namespace App\Filament\Widgets;

use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
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
            Stat::make('Active Courses', number_format($activeCourses))
                ->description('Published courses')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Total Learners', number_format($enrolledStudents))
                ->description('Enrolled Sri Lankan students')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Enrollments', number_format($totalEnrollments))
                ->description('Total course seats')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),

            Stat::make('Total Revenue', 'LKR '.number_format($totalRevenue, 2))
                ->description($isAdmin ? 'All settled payments' : 'Your instructed courses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Campus Attendance', $attendanceRate.'%')
                ->description('Physical check-in rate')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color($attendanceRate >= 75 ? 'success' : 'danger'),

            Stat::make('Pending Grading', number_format($pendingGrading))
                ->description('Submissions needing review')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color($pendingGrading > 0 ? 'warning' : 'gray'),
        ];
    }
}
