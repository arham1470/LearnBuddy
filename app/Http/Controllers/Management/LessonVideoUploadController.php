<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\Video\Exceptions\VideoProviderException;
use App\Services\Video\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonVideoUploadController extends Controller
{
    /**
     * Request a secure direct-upload session with private signed playback settings.
     */
    public function createUploadSession(Request $request, Course $course, Lesson $lesson, VideoService $videoService): JsonResponse
    {
        Gate::authorize('uploadVideo', $course);

        // Strict hierarchy check
        $lesson->loadMissing('section');
        if (! $lesson->section || (int) $lesson->section->course_id !== (int) $course->id) {
            abort(404, 'The requested lesson does not belong to this course.');
        }

        if (! $videoService->getProvider()->isConfigured()) {
            return response()->json([
                'error' => 'provider_unconfigured',
                'provider' => $videoService->getProvider()->getProviderName(),
                'message' => 'Video streaming provider credentials (Mux) are not configured in .env. Real uploads require MUX_TOKEN_ID, MUX_TOKEN_SECRET, and MUX signing keys.',
            ], 422);
        }

        try {
            $session = $videoService->initiateDirectUpload($lesson);

            return response()->json([
                'success' => true,
                'upload_url' => $session['upload_url'],
                'upload_id' => $session['upload_id'],
                'video_provider' => $videoService->getProvider()->getProviderName(),
                'message' => 'Direct upload session created. Upload file directly to Mux using PUT request.',
            ]);
        } catch (VideoProviderException $e) {
            return response()->json([
                'error' => 'provider_api_error',
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
