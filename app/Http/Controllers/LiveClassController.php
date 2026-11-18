<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LiveClass;
use App\Services\LiveClass\Contracts\LiveClassProviderInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function __construct(
        protected LiveClassProviderInterface $meetingProvider
    ) {}

    /**
     * Display course live classes schedule for enrolled learners.
     */
    public function index(Course $course): View
    {
        Gate::authorize('viewAny', [LiveClass::class, $course]);

        $liveClasses = $course->liveClasses()
            ->with(['instructor', 'batch'])
            ->orderBy('scheduled_start_time')
            ->get();

        return view('live_classes.index', compact('course', 'liveClasses'));
    }

    /**
     * Display session briefing, syllabus agenda, countdown, and join eligibility.
     */
    public function show(Course $course, LiveClass $liveClass): View
    {
        Gate::authorize('view', $liveClass);

        $liveClass->load(['instructor', 'batch', 'course']);

        $user = auth()->user();
        $canJoin = $liveClass->canBeJoinedBy($user);
        $timeStatus = $liveClass->timeWindowStatus($user);

        return view('live_classes.show', compact('course', 'liveClass', 'canJoin', 'timeStatus'));
    }

    /**
     * Join the interactive live class room.
     * Enforces role-based permissions, time boundaries, and student privacy.
     */
    public function join(Course $course, LiveClass $liveClass): View
    {
        $user = auth()->user();

        // 1. Authorize policy join permission
        Gate::authorize('join', $liveClass);

        // 2. Enforce session state & time boundary check
        if (! $liveClass->canBeJoinedBy($user)) {
            abort(403, 'This live class cannot be joined at this time. Please check the session schedule.');
        }

        // 3. Determine if user is host/moderator
        $isModerator = $user->isAdmin()
            || ((int) $liveClass->instructor_id === (int) $user->id)
            || $course->hasInstructor($user);

        // If host arrives for a scheduled meeting, transition status to live
        if ($isModerator && $liveClass->isScheduled()) {
            $liveClass->startLive();
        }

        // 4. Generate sanitized client meeting configuration (Never exposes administrative keys to students)
        $meetingConfig = $this->meetingProvider->getClientMeetingConfig($liveClass, $user, $isModerator);

        return view('live_classes.room', compact('course', 'liveClass', 'meetingConfig', 'isModerator'));
    }

    /**
     * End an active live classroom session (Host only).
     */
    public function end(Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('end', $liveClass);

        $liveClass->endLive();

        return redirect()->route('courses.live-classes.show', [$course->slug, $liveClass])
            ->with('status', 'Live classroom session ended successfully.');
    }
}
