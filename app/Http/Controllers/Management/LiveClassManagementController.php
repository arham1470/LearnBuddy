<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLiveClassRequest;
use App\Http\Requests\UpdateLiveClassRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\User;
use App\Notifications\LiveClassReminderNotification;
use App\Notifications\LiveClassStatusNotification;
use App\Services\LiveClass\Contracts\LiveClassProviderInterface;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LiveClassManagementController extends Controller
{
    public function __construct(
        protected LiveClassProviderInterface $meetingProvider
    ) {}

    /**
     * Display a listing of live classes for a course.
     */
    public function index(Course $course): View
    {
        Gate::authorize('create', [LiveClass::class, $course]);

        $liveClasses = $course->liveClasses()
            ->with(['instructor', 'batch'])
            ->latest('scheduled_start_time')
            ->get();

        $isProviderConfigured = $this->meetingProvider->isConfigured();
        $unconfiguredReason = $this->meetingProvider->getUnconfiguredReason();

        return view('management.live_classes.index', compact('course', 'liveClasses', 'isProviderConfigured', 'unconfiguredReason'));
    }

    /**
     * Show the form for scheduling a new live class.
     */
    public function create(Course $course): View
    {
        Gate::authorize('create', [LiveClass::class, $course]);

        $instructors = $course->instructors()->get();
        if ($instructors->isEmpty()) {
            $instructors = collect([auth()->user()]);
        }

        $batches = $course->batches()->get();

        return view('management.live_classes.create', compact('course', 'instructors', 'batches'));
    }

    /**
     * Store a newly scheduled live class.
     */
    public function store(StoreLiveClassRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [LiveClass::class, $course]);

        $data = $request->validated();
        $data['course_id'] = $course->id;

        // Generate meeting room details using the abstracted provider
        $dummyClass = new LiveClass(['course_id' => $course->id]);
        $roomDetails = $this->meetingProvider->createMeetingRoom($dummyClass);

        $data['meeting_identifier'] = $roomDetails['meeting_identifier'];
        $data['provider'] = $roomDetails['provider'];
        $data['status'] = LiveClass::STATUS_SCHEDULED;

        $liveClass = LiveClass::create($data);

        return redirect()->route('management.courses.live-classes.show', [$course, $liveClass])
            ->with('status', "Live class '{$liveClass->title}' scheduled successfully.");
    }

    /**
     * Display the host / management console for a live class.
     */
    public function show(Course $course, LiveClass $liveClass): View
    {
        Gate::authorize('view', $liveClass);

        $liveClass->load(['instructor', 'batch', 'course']);

        $isProviderConfigured = $this->meetingProvider->isConfigured();
        $unconfiguredReason = $this->meetingProvider->getUnconfiguredReason();

        return view('management.live_classes.show', compact('course', 'liveClass', 'isProviderConfigured', 'unconfiguredReason'));
    }

    /**
     * Show the form for editing/rescheduling a live class.
     */
    public function edit(Course $course, LiveClass $liveClass): View
    {
        Gate::authorize('update', $liveClass);

        $instructors = $course->instructors()->get();
        if ($instructors->isEmpty()) {
            $instructors = collect([$liveClass->instructor]);
        }

        $batches = $course->batches()->get();

        return view('management.live_classes.edit', compact('course', 'liveClass', 'instructors', 'batches'));
    }

    /**
     * Update/reschedule the live class.
     */
    public function update(UpdateLiveClassRequest $request, Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('update', $liveClass);

        $liveClass->update($request->validated());

        return redirect()->route('management.courses.live-classes.show', [$course, $liveClass])
            ->with('status', 'Live class details updated successfully.');
    }

    /**
     * Cancel a live class with reason.
     */
    public function cancel(Request $request, Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('cancel', $liveClass);

        $reason = $request->input('cancellation_reason', 'Live class cancelled by instructor.');
        $liveClass->cancel($reason);

        // Notify enrolled students
        $dispatcher = app(NotificationDispatcherService::class);
        $students = User::whereHas('enrollments', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', Enrollment::STATUS_ACTIVE);
        })->get();

        foreach ($students as $student) {
            $dispatcher->send(
                user: $student,
                notification: new LiveClassStatusNotification($liveClass, 'cancelled', $reason),
                deduplicationKey: "live_class_cancelled_{$liveClass->id}_{$student->id}",
                eventType: 'live_class_status'
            );
        }

        return redirect()->route('management.courses.live-classes.show', [$course, $liveClass])
            ->with('status', 'Live class session cancelled.');
    }

    /**
     * End an active live session and mark as completed.
     */
    public function end(Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('end', $liveClass);

        $liveClass->endLive();

        return redirect()->route('management.courses.live-classes.show', [$course, $liveClass])
            ->with('status', 'Live class session has been completed.');
    }

    /**
     * Delete an un-commenced live class.
     */
    public function destroy(Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('delete', $liveClass);

        $liveClass->delete();

        return redirect()->route('management.courses.live-classes.index', $course)
            ->with('status', 'Live class removed.');
    }

    /**
     * Trigger reminder notification hook for enrolled attendees.
     */
    public function triggerReminder(Course $course, LiveClass $liveClass): RedirectResponse
    {
        Gate::authorize('update', $liveClass);

        $liveClass->update(['reminder_sent_at' => now()]);

        // Dispatch reminders to active students
        $dispatcher = app(NotificationDispatcherService::class);
        $students = User::whereHas('enrollments', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', Enrollment::STATUS_ACTIVE);
        })->get();

        foreach ($students as $student) {
            $dispatcher->send(
                user: $student,
                notification: new LiveClassReminderNotification($liveClass, 30),
                deduplicationKey: "live_class_reminder_{$liveClass->id}_{$student->id}_manual",
                eventType: 'live_class_reminder'
            );
        }

        return back()->with('status', 'Class session reminders dispatched to enrolled students.');
    }
}
