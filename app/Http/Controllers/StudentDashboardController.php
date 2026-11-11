<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Contracts\View\View;

class StudentDashboardController extends Controller
{
    /**
     * Display the authenticated student's dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // 1. Active enrollments (Access granted)
        $activeEnrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->active()
            ->with([
                'course.category',
                'course.instructors',
                'course.sections.lessons',
                'deliveryOption',
                'batch.location',
            ])
            ->latest('enrolled_at')
            ->get();

        // 2. Pending payment enrollments (Seats reserved pending payment in Phase 13)
        $pendingEnrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->pendingPayment()
            ->with([
                'course.category',
                'deliveryOption',
                'batch.location',
            ])
            ->latest()
            ->get();

        // 3. Physical classroom batch schedules
        $physicalEnrollments = $activeEnrollments->filter(fn ($e) => $e->isPhysical() && $e->batch);

        // 4. Continue learning course (the most recently active enrollment)
        $continueLearning = $activeEnrollments->first();

        // 5. Activity log / notifications derived from real user events
        $recentActivities = Enrollment::query()
            ->where('user_id', $user->id)
            ->with(['course'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Pending assignments and quiz activity for enrolled courses
        $enrolledCourseIds = $activeEnrollments->pluck('course_id');

        $pendingAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->whereDoesntHave('submissions', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('course')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentQuizAttempts = QuizAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.student', compact(
            'user',
            'activeEnrollments',
            'pendingEnrollments',
            'physicalEnrollments',
            'continueLearning',
            'recentActivities',
            'pendingAssignments',
            'recentQuizAttempts'
        ));
    }
}
