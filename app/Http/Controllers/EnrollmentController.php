<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Notifications\EnrollmentConfirmationNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    /**
     * Show the enrollment checkout / confirmation preview screen.
     */
    public function create(Request $request, Course $course): View|RedirectResponse
    {
        if (! $course->isPublished()) {
            abort(404, 'The requested course is not available for enrollment.');
        }

        $user = auth()->user();

        // Check if user is already enrolled
        if ($user->isEnrolledIn($course)) {
            $existing = $user->getEnrollmentFor($course);

            return redirect()->route('enrollments.confirmation', $existing)
                ->with('info', 'You already have an enrollment for this course.');
        }

        $course->load([
            'category',
            'deliveryOptions' => fn ($q) => $q->where('is_active', true)->with(['batches.location']),
        ]);

        $selectedOptionId = (int) $request->query('option_id', $course->deliveryOptions->first()?->id);
        $selectedOption = $course->deliveryOptions->firstWhere('id', $selectedOptionId)
            ?? $course->deliveryOptions->first();

        return view('enrollments.create', compact('course', 'selectedOption'));
    }

    /**
     * Process course enrollment with safe transaction and concurrency locking.
     */
    public function store(StoreEnrollmentRequest $request, Course $course): RedirectResponse
    {
        if (! $course->isPublished()) {
            abort(404, 'The requested course is not available for enrollment.');
        }

        $user = auth()->user();

        $enrollment = DB::transaction(function () use ($request, $course, $user) {
            // 1. Prevent duplicate enrollments with row-level lock
            $existing = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_PENDING_PAYMENT])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'delivery_option_id' => 'You already have an active or pending enrollment for this course.',
                ]);
            }

            // 2. Re-verify delivery option from server database (never trust client price/details)
            $deliveryOption = CourseDeliveryOption::where('id', $request->validated('delivery_option_id'))
                ->where('course_id', $course->id)
                ->where('is_active', true)
                ->first();

            if (! $deliveryOption) {
                throw ValidationException::withMessages([
                    'delivery_option_id' => 'The selected delivery option is unavailable or no longer active.',
                ]);
            }

            // 3. Concurrency check for physical classroom batch
            $batch = null;
            $batchId = $request->validated('course_batch_id');

            if ($deliveryOption->delivery_mode === 'physical' || $batchId) {
                if (! $batchId) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'Please select an available physical campus batch to complete enrollment.',
                    ]);
                }

                // Pessimistic lock on the batch row to prevent overbooking race conditions
                $batch = CourseBatch::where('id', $batchId)
                    ->where('course_delivery_option_id', $deliveryOption->id)
                    ->lockForUpdate()
                    ->first();

                if (! $batch) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'The selected physical batch does not exist for this delivery option.',
                    ]);
                }

                if ($batch->isFull() || $batch->enrolled_seats >= $batch->max_seats) {
                    throw ValidationException::withMessages([
                        'course_batch_id' => "Physical batch '{$batch->batch_code}' is currently full. Please choose another batch or join an online intake.",
                    ]);
                }

                if ($batch->status !== 'open_for_enrollment') {
                    throw ValidationException::withMessages([
                        'course_batch_id' => 'This physical batch is currently closed for new enrollments.',
                    ]);
                }

                // Reserve the seat immediately inside the transaction
                $batch->increment('enrolled_seats');
            }

            // 4. Concurrency check for options with global max capacity
            if ($deliveryOption->max_capacity) {
                $enrolledCount = Enrollment::where('course_delivery_option_id', $deliveryOption->id)
                    ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_PENDING_PAYMENT])
                    ->lockForUpdate()
                    ->count();

                if ($enrolledCount >= $deliveryOption->max_capacity) {
                    throw ValidationException::withMessages([
                        'delivery_option_id' => 'This course delivery option has reached its maximum enrollment capacity.',
                    ]);
                }
            }

            // 5. Determine free vs paid status
            $isFree = $deliveryOption->is_free || (float) $deliveryOption->price === 0.0;
            $pricePaid = $isFree ? 0.00 : (float) $deliveryOption->price;
            $status = $isFree ? Enrollment::STATUS_ACTIVE : Enrollment::STATUS_PENDING_PAYMENT;
            $enrolledAt = $isFree ? now() : null;
            $expiresAt = ($isFree && $deliveryOption->access_period_days)
                ? now()->addDays($deliveryOption->access_period_days)
                : null;

            $enrollmentCode = 'ENR-'.date('Ymd').'-'.strtoupper(Str::random(6));

            return Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'course_delivery_option_id' => $deliveryOption->id,
                'course_batch_id' => $batch?->id,
                'enrollment_code' => $enrollmentCode,
                'delivery_mode' => $deliveryOption->delivery_mode,
                'status' => $status,
                'price_paid' => $pricePaid,
                'currency' => $deliveryOption->currency ?: 'LKR',
                'is_free' => $isFree,
                'enrolled_at' => $enrolledAt,
                'expires_at' => $expiresAt,
            ]);
        });

        if ($enrollment->isActive()) {
            app(NotificationDispatcherService::class)->send(
                user: $user,
                notification: new EnrollmentConfirmationNotification($enrollment),
                deduplicationKey: "enrollment_confirmed_{$enrollment->id}_{$user->id}",
                eventType: 'enrollment_confirmation'
            );
        }

        $message = $enrollment->isActive()
            ? 'Congratulations! Your enrollment is confirmed and your learning access is now active.'
            : 'Enrollment submitted! Your seat is reserved pending fee verification.';

        return redirect()->route('enrollments.confirmation', $enrollment)->with('success', $message);
    }

    /**
     * Display the enrollment confirmation receipt.
     */
    public function confirmation(Enrollment $enrollment): View
    {
        Gate::authorize('view', $enrollment);

        $enrollment->load([
            'course.category',
            'course.instructors',
            'deliveryOption',
            'batch.location',
        ]);

        return view('enrollments.confirmation', compact('enrollment'));
    }
}
