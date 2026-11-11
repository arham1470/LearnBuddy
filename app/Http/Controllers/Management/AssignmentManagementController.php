<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeSubmissionRequest;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AssignmentManagementController extends Controller
{
    /**
     * Display a listing of course assignments for instructors/admins.
     */
    public function index(Course $course): View
    {
        Gate::authorize('create', [Assignment::class, $course]);

        $assignments = $course->assignments()
            ->withCount('submissions')
            ->with(['section', 'lesson'])
            ->latest()
            ->get();

        return view('management.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show form to create a new course assignment.
     */
    public function create(Course $course): View
    {
        Gate::authorize('create', [Assignment::class, $course]);

        $course->load(['sections.lessons']);

        return view('management.assignments.create', compact('course'));
    }

    /**
     * Store a newly created assignment.
     */
    public function store(StoreAssignmentRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [Assignment::class, $course]);

        $data = $request->validated();
        $data['course_id'] = $course->id;
        $data['created_by_user_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['allow_late_submissions'] = $request->boolean('allow_late_submissions', false);

        Assignment::create($data);

        return redirect()->route('management.courses.assignments.index', $course)
            ->with('status', 'Assignment created successfully.');
    }

    /**
     * Show form to edit an existing assignment.
     */
    public function edit(Course $course, Assignment $assignment): View
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('update', $assignment);

        $course->load(['sections.lessons']);

        return view('management.assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Update an existing assignment.
     */
    public function update(UpdateAssignmentRequest $request, Course $course, Assignment $assignment): RedirectResponse
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('update', $assignment);

        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['allow_late_submissions'] = $request->boolean('allow_late_submissions', false);

        $assignment->update($data);

        return redirect()->route('management.courses.assignments.index', $course)
            ->with('status', 'Assignment updated successfully.');
    }

    /**
     * Delete an assignment.
     */
    public function destroy(Course $course, Assignment $assignment): RedirectResponse
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('delete', $assignment);

        $assignment->delete();

        return redirect()->route('management.courses.assignments.index', $course)
            ->with('status', 'Assignment deleted successfully.');
    }

    /**
     * View submissions list for an assignment.
     */
    public function submissions(Course $course, Assignment $assignment): View
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('update', $assignment);

        $submissions = $assignment->submissions()
            ->with(['student', 'grader'])
            ->latest('submitted_at')
            ->get();

        return view('management.assignments.submissions', compact('course', 'assignment', 'submissions'));
    }

    /**
     * Grade a student submission.
     */
    public function grade(GradeSubmissionRequest $request, Course $course, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse
    {
        if ((int) $assignment->course_id !== (int) $course->id || (int) $submission->assignment_id !== (int) $assignment->id) {
            abort(404, 'Submission not found for this assignment.');
        }

        Gate::authorize('grade', $assignment);

        $submission->update([
            'points_awarded' => $request->input('points_awarded'),
            'feedback' => $request->input('feedback'),
            'graded_by_user_id' => auth()->id(),
            'graded_at' => now(),
            'status' => AssignmentSubmission::STATUS_GRADED,
        ]);

        return back()->with('status', 'Submission graded successfully.');
    }
}
