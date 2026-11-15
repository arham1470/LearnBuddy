<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonResource;
use App\Services\Certificates\CertificateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LearningController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {}
    /**
     * Entry point: redirects the student to their next accessible lesson in the course.
     */
    public function index(Course $course): RedirectResponse|View
    {
        Gate::authorize('learn', $course);

        $user = auth()->user();
        $targetLesson = $course->firstAccessibleLesson($user);

        if (! $targetLesson) {
            // Course has no lessons or no published lessons
            return view('learning.empty', compact('course'));
        }

        return redirect()->route('learning.show', [$course, $targetLesson]);
    }

    /**
     * Show the main learning interface for a specific lesson.
     */
    public function show(Course $course, Lesson $lesson): View
    {
        Gate::authorize('learn', $course);

        $user = auth()->user();

        // 1. Enforce strict hierarchy: lesson must belong to a section of this course
        $lesson->loadMissing('section');
        if (! $lesson->section || (int) $lesson->section->course_id !== (int) $course->id) {
            abort(404, 'The requested lesson does not belong to this course.');
        }

        // 2. Unpublished lesson access control: only admin or course instructor can view unpublished lessons
        $isStaff = $user->isAdmin() || $course->hasInstructor($user);
        if (! $lesson->is_published && ! $isStaff) {
            abort(403, 'This lesson is not yet published.');
        }

        // 3. Load curriculum sections & lessons
        $sectionsQuery = $course->sections()->orderBy('sort_order');
        if (! $isStaff) {
            $sectionsQuery->where('is_published', true);
        }

        $sections = $sectionsQuery->with(['lessons' => function ($query) use ($isStaff) {
            if (! $isStaff) {
                $query->where('is_published', true);
            }
            $query->orderBy('sort_order');
        }])->get();

        // 4. Flatten accessible lessons to determine navigation (previous / next)
        $accessibleLessons = $sections->pluck('lessons')->flatten();
        $currentIndex = $accessibleLessons->search(fn ($item) => (int) $item->id === (int) $lesson->id);

        $previousLesson = ($currentIndex !== false && $currentIndex > 0)
            ? $accessibleLessons->get($currentIndex - 1)
            : null;

        $nextLesson = ($currentIndex !== false && $currentIndex < $accessibleLessons->count() - 1)
            ? $accessibleLessons->get($currentIndex + 1)
            : null;

        // 5. Load resources for the current lesson
        $lesson->load('resources');

        // 6. User's progress & notes for this lesson
        $lessonProgress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        // 7. Completed lesson IDs across the whole course for the sidebar checkmarks
        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // 8. Overall course completion percentage
        $progressPercentage = $course->calculateProgressPercentageFor($user);

        // 9. Certificate status & eligibility for quick access
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $certificateEligibility = $this->certificateService->checkEligibility($user, $course);
        $isCertificateEligible = (bool) ($certificateEligibility['is_eligible'] ?? false);

        return view('learning.show', compact(
            'course',
            'lesson',
            'sections',
            'accessibleLessons',
            'previousLesson',
            'nextLesson',
            'lessonProgress',
            'completedLessonIds',
            'progressPercentage',
            'certificate',
            'isCertificateEligible'
        ));
    }

    /**
     * Update or toggle lesson progress (completion status, notes, last watched seconds).
     */
    public function updateProgress(Request $request, Course $course, Lesson $lesson): JsonResponse|RedirectResponse
    {
        Gate::authorize('learn', $course);

        $user = auth()->user();

        // Strict integrity check
        $lesson->loadMissing('section');
        if (! $lesson->section || (int) $lesson->section->course_id !== (int) $course->id) {
            abort(404, 'The requested lesson does not belong to this course.');
        }

        // Unpublished check
        $isStaff = $user->isAdmin() || $course->hasInstructor($user);
        if (! $lesson->is_published && ! $isStaff) {
            abort(403, 'Cannot update progress on an unpublished lesson.');
        }

        $validated = $request->validate([
            'is_completed' => 'nullable|boolean',
            'notes' => 'nullable|string|max:10000',
            'last_watched_seconds' => 'nullable|integer|min:0|max:86400',
        ]);

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        if ($request->has('is_completed')) {
            $isCompleted = (bool) $request->input('is_completed');
            $progress->is_completed = $isCompleted;
            $progress->completed_at = $isCompleted ? now() : null;
        }

        if ($request->has('notes')) {
            $progress->notes = $request->input('notes');
        }

        if ($request->has('last_watched_seconds')) {
            $progress->last_watched_seconds = (int) $request->input('last_watched_seconds');
        }

        $progress->save();

        $courseProgressPercentage = $course->calculateProgressPercentageFor($user);

        // Certificate status & eligibility verification
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $eligibility = $this->certificateService->checkEligibility($user, $course);
        $isEligible = (bool) ($eligibility['is_eligible'] ?? false);
        $certificateUrl = null;

        if ($certificate) {
            $certificateUrl = route('certificates.show', $certificate);
        } elseif ($isEligible) {
            $certificateUrl = route('certificates.eligibility', $course);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_completed' => $progress->is_completed,
                'completed_at' => $progress->completed_at?->toISOString(),
                'notes' => $progress->notes,
                'course_progress_percentage' => $courseProgressPercentage,
                'has_certificate' => (bool) $certificate,
                'is_certificate_eligible' => $isEligible,
                'certificate_url' => $certificateUrl,
                'message' => $progress->is_completed ? 'Lesson marked as complete!' : 'Lesson marked as incomplete.',
            ]);
        }

        $statusMsg = $progress->is_completed ? 'Lesson marked as complete!' : 'Progress updated.';

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Download or view a lesson resource.
     */
    public function downloadResource(Course $course, Lesson $lesson, LessonResource $resource): RedirectResponse|BinaryFileResponse
    {
        Gate::authorize('learn', $course);

        // Hierarchical integrity
        $lesson->loadMissing('section');
        if (! $lesson->section || (int) $lesson->section->course_id !== (int) $course->id) {
            abort(404, 'The requested lesson does not belong to this course.');
        }

        if ((int) $resource->lesson_id !== (int) $lesson->id) {
            abort(404, 'Resource not found for this lesson.');
        }

        $user = auth()->user();
        $isStaff = $user->isAdmin() || $course->hasInstructor($user);
        if (! $lesson->is_published && ! $isStaff) {
            abort(403, 'Cannot access resources of an unpublished lesson.');
        }

        if (! $resource->is_downloadable && ! empty($resource->external_url)) {
            return redirect()->away($resource->external_url);
        }

        if (! empty($resource->external_url)) {
            return redirect()->away($resource->external_url);
        }

        if ($resource->file_path) {
            $disk = Storage::disk('public');
            if ($disk->exists($resource->file_path)) {
                return response()->download($disk->path($resource->file_path), $resource->file_name ?? basename($resource->file_path));
            }
        }

        abort(404, 'Requested file resource could not be found.');
    }
}
