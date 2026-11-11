<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CourseBatch;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentBatchController extends Controller
{
    /**
     * Display the student's enrolled physical batch schedule and personal attendance sheet.
     * Enforces strict student privacy.
     */
    public function show(CourseBatch $batch): View
    {
        $user = auth()->user();

        Gate::authorize('view', $batch);

        $enrollment = $batch->enrollments()->where('user_id', $user->id)->first();

        // If user is a student, ensure they are enrolled in this batch
        if ($user->isStudent() && ! $enrollment) {
            abort(403, 'You are not enrolled in this physical campus batch.');
        }

        $batch->load([
            'location',
            'deliveryOption.course',
            'schedules',
            'sessions' => fn ($q) => $q->orderBy('session_date')->orderBy('start_time'),
        ]);

        // Query personal attendance records only for this student
        $myAttendances = Attendance::where('user_id', $user->id)
            ->whereIn('batch_session_id', $batch->sessions->pluck('id'))
            ->get()
            ->keyBy('batch_session_id');

        $attendanceRate = $enrollment ? $enrollment->attendanceRate() : 0.0;

        return view('batches.student-show', compact('batch', 'enrollment', 'myAttendances', 'attendanceRate'));
    }
}
