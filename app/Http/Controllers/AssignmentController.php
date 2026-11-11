<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    /**
     * Display the assignment detail and student submission screen.
     */
    public function show(Course $course, Assignment $assignment): View
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('view', $assignment);

        $user = auth()->user();
        $submission = $assignment->getUserSubmission($user);

        return view('assignments.show', compact('course', 'assignment', 'submission'));
    }

    /**
     * Submit or re-upload a submission for an assignment.
     */
    public function submit(SubmitAssignmentRequest $request, Course $course, Assignment $assignment): RedirectResponse
    {
        if ((int) $assignment->course_id !== (int) $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        Gate::authorize('submit', $assignment);

        $user = auth()->user();
        $existing = $assignment->getUserSubmission($user);

        if ($existing && $existing->isGraded()) {
            return back()->with('error', 'This assignment has already been graded and can no longer be updated.');
        }

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeOriginalName = Str::slug($originalName).'.'.strtolower($extension);

        // Store securely on private local disk
        $path = $file->store("assignments/submissions/{$assignment->id}", 'local');

        // Delete old file if re-uploading
        if ($existing && $existing->file_path && Storage::disk('local')->exists($existing->file_path)) {
            Storage::disk('local')->delete($existing->file_path);
        }

        $isLate = $assignment->isPastDue();

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
            ],
            [
                'file_path' => $path,
                'file_original_name' => $safeOriginalName,
                'file_size_bytes' => $file->getSize(),
                'file_mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'student_notes' => $request->input('student_notes'),
                'submitted_at' => now(),
                'is_late' => $isLate,
                'status' => AssignmentSubmission::STATUS_SUBMITTED,
            ]
        );

        $message = $isLate
            ? 'Assignment submitted (marked as late submission).'
            : 'Assignment submitted successfully!';

        return redirect()->route('courses.assignments.show', [$course, $assignment])
            ->with('status', $message);
    }

    /**
     * Securely download a submission file.
     */
    public function downloadSubmission(AssignmentSubmission $submission): StreamedResponse
    {
        Gate::authorize('viewSubmission', $submission);

        if (! Storage::disk('local')->exists($submission->file_path)) {
            abort(404, 'The requested submission file could not be found on the server.');
        }

        return Storage::disk('local')->download($submission->file_path, $submission->file_original_name);
    }
}
