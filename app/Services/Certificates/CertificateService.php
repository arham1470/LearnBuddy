<?php

namespace App\Services\Certificates;

use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CertificateService
{
    public function __construct(
        protected NotificationDispatcherService $notificationDispatcher
    ) {}

    /**
     * Determine server-side certificate eligibility for a user in a course.
     *
     * @return array{
     *     is_eligible: bool,
     *     enrollment: ?Enrollment,
     *     total_lessons: int,
     *     completed_lessons: int,
     *     lessons_percentage: float,
     *     total_assignments: int,
     *     completed_assignments: int,
     *     total_quizzes: int,
     *     passed_quizzes: int,
     *     final_grade: float,
     *     missing_requirements: array<string>
     * }
     */
    public function checkEligibility(User $user, Course $course): array
    {
        $missingRequirements = [];

        // 1. Enrollment Verification
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->first();

        if (! $enrollment) {
            $missingRequirements[] = 'You must have an active enrolment in this course.';

            return $this->buildEligibilityPayload(false, null, 0, 0, 0.0, 0, 0, 0, 0, 0.0, $missingRequirements);
        }

        // Check if delivery option supports certificates
        if ($enrollment->deliveryOption && ! $enrollment->deliveryOption->includes_certificate) {
            $missingRequirements[] = 'Your selected course delivery option does not issue a completion certificate.';

            return $this->buildEligibilityPayload(false, $enrollment, 0, 0, 0.0, 0, 0, 0, 0, 0.0, $missingRequirements);
        }

        // 2. Lessons Completion Verification
        $lessonIds = $course->lessons()->pluck('lessons.id');
        $totalLessons = $lessonIds->count();
        $completedLessons = 0;

        if ($totalLessons > 0) {
            $completedLessons = LessonProgress::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->count();

            if ($completedLessons < $totalLessons) {
                $missingRequirements[] = "Complete all lessons ({$completedLessons}/{$totalLessons} finished).";
            }
        }

        $lessonsPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 100.0;

        // 3. Published Assignments Verification
        $publishedAssignments = $course->publishedAssignments()->get();
        $totalAssignments = $publishedAssignments->count();
        $completedAssignments = 0;
        $assignmentScores = [];

        if ($totalAssignments > 0) {
            $gradedSubmissions = AssignmentSubmission::where('user_id', $user->id)
                ->whereIn('assignment_id', $publishedAssignments->pluck('id'))
                ->where('status', AssignmentSubmission::STATUS_GRADED)
                ->with('assignment')
                ->get();

            $completedAssignments = $gradedSubmissions->count();

            foreach ($gradedSubmissions as $sub) {
                $maxPoints = $sub->assignment?->max_points ?? 100;
                if ($maxPoints > 0) {
                    $assignmentScores[] = ($sub->points_awarded / $maxPoints) * 100;
                }
            }

            if ($completedAssignments < $totalAssignments) {
                $missingRequirements[] = "Submit and receive grades for all assignments ({$completedAssignments}/{$totalAssignments} graded).";
            }
        }

        // 4. Published Quizzes Verification
        $publishedQuizzes = $course->publishedQuizzes()->get();
        $totalQuizzes = $publishedQuizzes->count();
        $passedQuizzes = 0;
        $quizScores = [];

        if ($totalQuizzes > 0) {
            $passedAttempts = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $publishedQuizzes->pluck('id'))
                ->where('is_passed', true)
                ->get()
                ->unique('quiz_id');

            $passedQuizzes = $passedAttempts->count();

            foreach ($passedAttempts as $attempt) {
                $quizScores[] = (float) $attempt->percentage;
            }

            if ($passedQuizzes < $totalQuizzes) {
                $missingRequirements[] = "Pass all published quizzes ({$passedQuizzes}/{$totalQuizzes} passed).";
            }
        }

        // 5. Final Grade Calculation
        $allScores = array_merge($assignmentScores, $quizScores);
        $finalGrade = ! empty($allScores)
            ? round(array_sum($allScores) / count($allScores), 2)
            : 100.00;

        $isEligible = empty($missingRequirements);

        return $this->buildEligibilityPayload(
            $isEligible,
            $enrollment,
            $totalLessons,
            $completedLessons,
            $lessonsPercentage,
            $totalAssignments,
            $completedAssignments,
            $totalQuizzes,
            $passedQuizzes,
            $finalGrade,
            $missingRequirements
        );
    }

    /**
     * Issue an authentic, non-guessable certificate to an eligible student.
     *
     * @throws ValidationException
     */
    public function issueCertificate(User $user, Course $course): Certificate
    {
        // 1. Check if already issued (idempotent, prevents duplicate issuance)
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // 2. Strict server-side verification of completion rules
        $eligibility = $this->checkEligibility($user, $course);

        if (! $eligibility['is_eligible']) {
            throw ValidationException::withMessages([
                'eligibility' => $eligibility['missing_requirements'][0] ?? 'Course completion criteria have not been fulfilled.',
            ]);
        }

        $enrollment = $eligibility['enrollment'];

        return DB::transaction(function () use ($user, $course, $enrollment, $eligibility) {
            $certificateCode = Certificate::generateCode();
            $verificationHash = Certificate::generateVerificationHash();

            $metadata = [
                'student_name' => $user->name,
                'course_title' => $course->title,
                'category_name' => $course->category?->name ?? 'General',
                'instructors' => $course->instructors()->pluck('name')->toArray(),
                'issued_at' => now()->toIso8601String(),
                'lessons_completed' => $eligibility['completed_lessons'],
                'total_lessons' => $eligibility['total_lessons'],
                'assignments_completed' => $eligibility['completed_assignments'],
                'quizzes_passed' => $eligibility['passed_quizzes'],
                'final_grade' => $eligibility['final_grade'],
            ];

            $certificate = Certificate::create([
                'certificate_code' => $certificateCode,
                'verification_hash' => $verificationHash,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id,
                'issue_date' => now()->toDateString(),
                'final_grade' => $eligibility['final_grade'],
                'metadata' => $metadata,
                'is_revoked' => false,
            ]);

            // Mark enrollment as completed
            $enrollment->update([
                'status' => Enrollment::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // Send notification
            $this->notificationDispatcher->send(
                user: $user,
                notification: new CertificateIssuedNotification($course, $certificateCode),
                deduplicationKey: "certificate_issued_{$certificate->id}_{$user->id}",
                eventType: 'certificate_issued'
            );

            return $certificate;
        });
    }

    /**
     * Find a certificate by its public cryptographic verification hash.
     */
    public function verifyByHash(string $hash): ?Certificate
    {
        return Certificate::where('verification_hash', $hash)
            ->with(['course.category', 'course.instructors', 'user'])
            ->first();
    }

    /**
     * Find a certificate by its unique public certificate code.
     */
    public function verifyByCode(string $code): ?Certificate
    {
        return Certificate::where('certificate_code', trim(strtoupper($code)))
            ->with(['course.category', 'course.instructors', 'user'])
            ->first();
    }

    protected function buildEligibilityPayload(
        bool $isEligible,
        ?Enrollment $enrollment,
        int $totalLessons,
        int $completedLessons,
        float $lessonsPercentage,
        int $totalAssignments,
        int $completedAssignments,
        int $totalQuizzes,
        int $passedQuizzes,
        float $finalGrade,
        array $missingRequirements
    ): array {
        return [
            'is_eligible' => $isEligible,
            'enrollment' => $enrollment,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'lessons_percentage' => $lessonsPercentage,
            'total_assignments' => $totalAssignments,
            'completed_assignments' => $completedAssignments,
            'total_quizzes' => $totalQuizzes,
            'passed_quizzes' => $passedQuizzes,
            'final_grade' => $finalGrade,
            'missing_requirements' => $missingRequirements,
        ];
    }
}
