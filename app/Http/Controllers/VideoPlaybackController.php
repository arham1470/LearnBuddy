<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Video\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VideoPlaybackController extends Controller
{
    /**
     * Issue an authorized, short-lived signed playback token for a course lesson.
     */
    public function getPlaybackData(Request $request, Course $course, Lesson $lesson, VideoService $videoService): JsonResponse
    {
        Gate::authorize('learn', $course);

        $user = auth()->user();

        // 1. Strict hierarchy validation (prevent cross-course IDOR)
        $lesson->loadMissing('section');
        if (! $lesson->section || (int) $lesson->section->course_id !== (int) $course->id) {
            abort(404, 'The requested lesson does not belong to this course.');
        }

        // 2. Lesson publication access control
        $isStaff = $user->isAdmin() || $course->hasInstructor($user);
        if (! $lesson->is_published && ! $isStaff) {
            abort(403, 'This lesson is not yet published.');
        }

        $playbackData = $videoService->getPlaybackDataForUser($lesson, $user, (string) $request->ip());

        return response()->json($playbackData);
    }
}
