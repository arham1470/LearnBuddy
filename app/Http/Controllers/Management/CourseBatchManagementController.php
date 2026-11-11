<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecordAttendanceRequest;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\StoreBatchScheduleRequest;
use App\Http\Requests\StoreBatchSessionRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\Attendance;
use App\Models\BatchSchedule;
use App\Models\BatchSession;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\PhysicalLocation;
use App\Notifications\BatchScheduleChangeNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseBatchManagementController extends Controller
{
    /**
     * Display a listing of course batches.
     */
    public function index(Course $course): View
    {
        Gate::authorize('create', [CourseBatch::class, $course]);

        $batches = CourseBatch::whereHas('deliveryOption', fn ($q) => $q->where('course_id', $course->id))
            ->with(['location', 'deliveryOption', 'schedules'])
            ->withCount(['sessions', 'activeEnrollments'])
            ->latest()
            ->get();

        return view('management.batches.index', compact('course', 'batches'));
    }

    /**
     * Show form to create a new physical batch.
     */
    public function create(Course $course): View
    {
        Gate::authorize('create', [CourseBatch::class, $course]);

        $deliveryOptions = $course->deliveryOptions()
            ->whereIn('delivery_mode', ['physical', 'hybrid'])
            ->where('is_active', true)
            ->get();

        $locations = PhysicalLocation::where('is_active', true)
            ->orderBy('province')
            ->orderBy('city')
            ->get();

        return view('management.batches.create', compact('course', 'deliveryOptions', 'locations'));
    }

    /**
     * Store a newly created batch.
     */
    public function store(StoreBatchRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('create', [CourseBatch::class, $course]);

        $data = $request->validated();

        $batch = CourseBatch::create($data);

        return redirect()->route('management.courses.batches.show', [$course, $batch])
            ->with('status', "Physical batch '{$batch->batch_code}' created successfully. Now configure weekly schedules and sessions below.");
    }

    /**
     * Display batch console: schedules, calendar sessions, students, and change logs.
     */
    public function show(Course $course, CourseBatch $batch): View
    {
        Gate::authorize('view', $batch);

        $batch->load([
            'location',
            'deliveryOption',
            'schedules',
            'sessions.conductedBy',
            'activeEnrollments.user',
            'scheduleChanges.changedBy',
        ]);

        return view('management.batches.show', compact('course', 'batch'));
    }

    /**
     * Show form to edit an existing batch.
     */
    public function edit(Course $course, CourseBatch $batch): View
    {
        Gate::authorize('update', $batch);

        $deliveryOptions = $course->deliveryOptions()
            ->whereIn('delivery_mode', ['physical', 'hybrid'])
            ->get();

        $locations = PhysicalLocation::where('is_active', true)->get();

        return view('management.batches.edit', compact('course', 'batch', 'deliveryOptions', 'locations'));
    }

    /**
     * Update an existing batch and log changes.
     */
    public function update(UpdateBatchRequest $request, Course $course, CourseBatch $batch): RedirectResponse
    {
        Gate::authorize('update', $batch);

        $prev = [
            'batch_name' => $batch->batch_name,
            'max_seats' => $batch->max_seats,
            'status' => $batch->status,
            'start_date' => $batch->start_date?->format('Y-m-d'),
            'end_date' => $batch->end_date?->format('Y-m-d'),
            'location_id' => $batch->location_id,
        ];

        $data = $request->validated();
        $reason = $request->input('change_reason');
        unset($data['change_reason']);

        $batch->update($data);

        $new = [
            'batch_name' => $batch->batch_name,
            'max_seats' => $batch->max_seats,
            'status' => $batch->status,
            'start_date' => $batch->start_date?->format('Y-m-d'),
            'end_date' => $batch->end_date?->format('Y-m-d'),
            'location_id' => $batch->location_id,
        ];

        $batch->logScheduleChange(auth()->user(), 'batch_updated', $prev, $new, $reason);

        return redirect()->route('management.courses.batches.show', [$course, $batch])
            ->with('status', 'Batch details updated successfully.');
    }

    /**
     * Cancel an active or upcoming batch.
     */
    public function cancel(Request $request, Course $course, CourseBatch $batch): RedirectResponse
    {
        Gate::authorize('update', $batch);

        $reason = $request->input('reason', 'Batch cancelled by instructor/administrator.');
        $batch->cancel(auth()->user(), $reason);

        // Notify enrolled students in this physical batch
        $dispatcher = app(NotificationDispatcherService::class);
        $enrolledStudents = $batch->enrollments()->with('user')->get()->pluck('user')->filter();
        foreach ($enrolledStudents as $student) {
            $dispatcher->send(
                user: $student,
                notification: new BatchScheduleChangeNotification($batch, 'cancelled', $reason),
                deduplicationKey: "batch_cancelled_{$batch->id}_{$student->id}",
                eventType: 'batch_cancelled'
            );
        }

        return redirect()->route('management.courses.batches.show', [$course, $batch])
            ->with('status', "Batch '{$batch->batch_code}' cancelled.");
    }

    /**
     * Delete an un-enrolled batch.
     */
    public function destroy(Course $course, CourseBatch $batch): RedirectResponse
    {
        Gate::authorize('delete', $batch);

        if ($batch->enrolled_seats > 0) {
            return back()->with('error', 'Cannot delete a batch that has enrolled students. You can cancel the batch instead.');
        }

        $batch->delete();

        return redirect()->route('management.courses.batches.index', $course)
            ->with('status', 'Batch deleted successfully.');
    }

    /**
     * Add a weekly recurring schedule slot.
     */
    public function storeSchedule(StoreBatchScheduleRequest $request, Course $course, CourseBatch $batch): RedirectResponse
    {
        Gate::authorize('update', $batch);

        $schedule = BatchSchedule::create([
            'batch_id' => $batch->id,
            'day_of_week' => $request->input('day_of_week'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'room_number' => $request->input('room_number'),
            'notes' => $request->input('notes'),
        ]);

        $batch->logScheduleChange(auth()->user(), 'schedule_slot_added', [], [
            'day_of_week' => $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'room_number' => $schedule->room_number,
        ]);

        return back()->with('status', 'Weekly schedule slot added.');
    }

    /**
     * Remove a weekly schedule slot.
     */
    public function destroySchedule(Course $course, CourseBatch $batch, BatchSchedule $schedule): RedirectResponse
    {
        Gate::authorize('update', $batch);

        if ((int) $schedule->batch_id !== (int) $batch->id) {
            abort(404);
        }

        $prev = [
            'day_of_week' => $schedule->day_of_week,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
        ];

        $schedule->delete();

        $batch->logScheduleChange(auth()->user(), 'schedule_slot_removed', $prev, []);

        return back()->with('status', 'Schedule slot removed.');
    }

    /**
     * Schedule a specific class session date.
     */
    public function storeSession(StoreBatchSessionRequest $request, Course $course, CourseBatch $batch): RedirectResponse
    {
        Gate::authorize('recordAttendance', $batch);

        BatchSession::create([
            'batch_id' => $batch->id,
            'session_date' => $request->input('session_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'topic' => $request->input('topic'),
            'room_number' => $request->input('room_number'),
            'status' => BatchSession::STATUS_SCHEDULED,
            'conducted_by_user_id' => auth()->id(),
        ]);

        return back()->with('status', 'Class session scheduled.');
    }

    /**
     * Display session attendance sheet for recording student presence.
     */
    public function attendanceSheet(Course $course, CourseBatch $batch, BatchSession $session): View
    {
        Gate::authorize('recordAttendance', $batch);

        if ((int) $session->batch_id !== (int) $batch->id) {
            abort(404);
        }

        $enrollments = $batch->activeEnrollments()->with('user')->get();
        $attendances = $session->attendances()->get()->keyBy('user_id');

        return view('management.batches.attendance', compact('course', 'batch', 'session', 'enrollments', 'attendances'));
    }

    /**
     * Save student attendance records for a session.
     */
    public function recordAttendance(RecordAttendanceRequest $request, Course $course, CourseBatch $batch, BatchSession $session): RedirectResponse
    {
        Gate::authorize('recordAttendance', $batch);

        if ((int) $session->batch_id !== (int) $batch->id) {
            abort(404);
        }

        DB::transaction(function () use ($request, $session, $batch) {
            $enrollments = $batch->activeEnrollments()->get()->keyBy('user_id');

            foreach ($request->input('attendances', []) as $entry) {
                $userId = $entry['user_id'];
                $enrollment = $enrollments->get($userId);

                if (! $enrollment) {
                    continue;
                }

                Attendance::updateOrCreate(
                    [
                        'batch_session_id' => $session->id,
                        'user_id' => $userId,
                    ],
                    [
                        'enrollment_id' => $enrollment->id,
                        'status' => $entry['status'],
                        'remarks' => $entry['remarks'] ?? null,
                        'recorded_by_user_id' => auth()->id(),
                    ]
                );
            }

            $session->update([
                'status' => BatchSession::STATUS_COMPLETED,
            ]);
        });

        return redirect()->route('management.courses.batches.show', [$course, $batch])
            ->with('status', "Attendance recorded for session on {$session->session_date->format('M d, Y')}.");
    }
}
