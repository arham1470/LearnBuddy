<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateAndReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected User $studentA;

    protected User $studentB;

    protected Course $course;

    protected CourseDeliveryOption $deliveryOption;

    protected Enrollment $enrollmentA;

    protected CourseSection $section;

    protected Lesson $lesson1;

    protected Lesson $lesson2;

    protected Assignment $assignment;

    protected Quiz $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@learnbuddy.lk']);
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create(['name' => 'Dr. Kamal Silva', 'email' => 'kamal@learnbuddy.lk']);
        $this->instructor->assignRole('instructor');

        $this->studentA = User::factory()->create([
            'name' => 'Nimal Perera',
            'email' => 'nimal.perera@learnbuddy.lk',
            'phone' => '+94771234567',
        ]);
        $this->studentA->assignRole('student');

        $this->studentB = User::factory()->create([
            'name' => 'Sunil Fernando',
            'email' => 'sunil.fernando@learnbuddy.lk',
            'phone' => '+94779876543',
        ]);
        $this->studentB->assignRole('student');

        $category = Category::create([
            'name' => 'Software Engineering',
            'slug' => 'software-engineering',
        ]);

        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Full-Stack Web Development',
            'slug' => 'full-stack-web-development',
            'summary' => 'Comprehensive masterclass on modern web architecture.',
            'description' => 'Detailed curriculum covering backend, frontend, and DevOps.',
            'level' => 'intermediate',
            'status' => 'published',
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);
        $this->course->instructors()->attach($this->instructor->id);

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'delivery_mode' => 'online',
            'title' => 'Online Self-Paced Masterclass',
            'price' => 15000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'includes_certificate' => true,
        ]);

        $this->enrollmentA = Enrollment::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-TEST-CERT-01',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 15000.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);

        $this->section = CourseSection::create([
            'course_id' => $this->course->id,
            'title' => 'Module 1: Foundations',
            'sort_order' => 1,
        ]);

        $this->lesson1 = Lesson::create([
            'section_id' => $this->section->id,
            'title' => 'Lesson 1: Introduction',
            'slug' => 'lesson-1-intro',
            'type' => 'text',
            'content' => 'Introduction content.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->lesson2 = Lesson::create([
            'section_id' => $this->section->id,
            'title' => 'Lesson 2: Architecture',
            'slug' => 'lesson-2-arch',
            'type' => 'text',
            'content' => 'Architecture content.',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $this->assignment = Assignment::create([
            'course_id' => $this->course->id,
            'course_section_id' => $this->section->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Milestone Project 1',
            'slug' => 'milestone-project-1',
            'description' => 'Build a backend API service.',
            'instructions' => 'Submit a zip archive containing your source code.',
            'max_points' => 100,
            'passing_points' => 50,
            'due_date' => now()->addDays(7),
            'is_published' => true,
        ]);

        $this->quiz = Quiz::create([
            'course_id' => $this->course->id,
            'course_section_id' => $this->section->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Module 1 Knowledge Check',
            'slug' => 'module-1-quiz',
            'description' => 'Test your understanding of Module 1 concepts.',
            'time_limit_minutes' => 30,
            'pass_percentage' => 70,
            'max_attempts' => 3,
            'is_published' => true,
        ]);
    }

    /**
     * Test student cannot claim certificate when lessons or assessments are incomplete.
     */
    public function test_student_ineligible_for_certificate_if_lessons_or_assessments_incomplete(): void
    {
        // Student only completes Lesson 1
        LessonProgress::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // Check eligibility page
        $response = $this->actingAs($this->studentA)->get(route('certificates.eligibility', $this->course));
        $response->assertStatus(200);
        $response->assertSee('Complete all lessons');

        // Attempt claiming certificate
        $claimResponse = $this->actingAs($this->studentA)->post(route('certificates.claim', $this->course));
        $claimResponse->assertSessionHasErrors('eligibility');

        // Assert no certificate was created
        $this->assertDatabaseCount('certificates', 0);
    }

    /**
     * Test student is eligible and successfully claims certificate when all requirements are fulfilled.
     */
    public function test_student_eligible_and_can_claim_certificate_when_all_requirements_completed(): void
    {
        // 1. Complete all lessons
        LessonProgress::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        LessonProgress::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson2->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // 2. Graded Assignment submission
        AssignmentSubmission::create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->studentA->id,
            'file_path' => 'submissions/proj1.zip',
            'file_original_name' => 'proj1.zip',
            'file_size_bytes' => 1024,
            'file_mime_type' => 'application/zip',
            'status' => AssignmentSubmission::STATUS_GRADED,
            'points_awarded' => 90,
            'submitted_at' => now(),
            'graded_at' => now(),
            'graded_by_user_id' => $this->instructor->id,
        ]);

        // 3. Passed Quiz Attempt
        QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->studentA->id,
            'attempt_number' => 1,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'score' => 85,
            'total_marks' => 100,
            'percentage' => 85.00,
            'is_passed' => true,
            'started_at' => now()->subMinutes(15),
            'completed_at' => now(),
        ]);

        // Eligibility view should now indicate ready to claim
        $eligibilityResponse = $this->actingAs($this->studentA)->get(route('certificates.eligibility', $this->course));
        $eligibilityResponse->assertStatus(200);
        $eligibilityResponse->assertSee('Claim Certificate of Completion');

        // Claim Certificate
        $claimResponse = $this->actingAs($this->studentA)->post(route('certificates.claim', $this->course));
        $claimResponse->assertRedirect();

        // Verify Certificate was created in database
        $this->assertDatabaseHas('certificates', [
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'is_revoked' => false,
        ]);

        $certificate = Certificate::where('user_id', $this->studentA->id)->first();
        $this->assertNotNull($certificate);
        $this->assertStringStartsWith('LB-CERT-', $certificate->certificate_code);
        $this->assertEquals(64, strlen($certificate->verification_hash)); // SHA-256 hash length

        // Enrollment should be marked completed
        $this->assertEquals(Enrollment::STATUS_COMPLETED, $this->enrollmentA->fresh()->status);
    }

    /**
     * Test duplicate certificate issuance is strictly prevented (Idempotent).
     */
    public function test_duplicate_certificate_issuance_is_strictly_prevented(): void
    {
        // Complete all requirements
        LessonProgress::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        LessonProgress::create([
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson2->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        AssignmentSubmission::create([
            'assignment_id' => $this->assignment->id,
            'user_id' => $this->studentA->id,
            'file_path' => 'submissions/proj1.zip',
            'file_original_name' => 'proj1.zip',
            'file_size_bytes' => 1024,
            'file_mime_type' => 'application/zip',
            'status' => AssignmentSubmission::STATUS_GRADED,
            'points_awarded' => 95,
            'submitted_at' => now(),
        ]);
        QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->studentA->id,
            'attempt_number' => 1,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'score' => 90,
            'total_marks' => 100,
            'percentage' => 90.00,
            'is_passed' => true,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now(),
        ]);

        // First claim
        $this->actingAs($this->studentA)->post(route('certificates.claim', $this->course));
        $this->assertDatabaseCount('certificates', 1);

        $firstCertificate = Certificate::first();

        // Second claim attempt
        $secondResponse = $this->actingAs($this->studentA)->post(route('certificates.claim', $this->course));
        $secondResponse->assertRedirect(route('certificates.show', $firstCertificate));

        // Still exactly 1 certificate in database
        $this->assertDatabaseCount('certificates', 1);
    }

    /**
     * Test public verification page reveals only intended credential info and protects student privacy.
     */
    public function test_public_verification_page_reveals_only_intended_data_and_protects_privacy(): void
    {
        $certificate = Certificate::create([
            'certificate_code' => 'LB-CERT-2026-TESTPRIV',
            'verification_hash' => hash('sha256', 'secure_hash_seed_123'),
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'enrollment_id' => $this->enrollmentA->id,
            'issue_date' => now()->toDateString(),
            'final_grade' => 92.50,
            'metadata' => ['student_name' => $this->studentA->name],
            'is_revoked' => false,
        ]);

        // Unauthenticated guest visits public verification URL
        $response = $this->get(route('certificates.verify', $certificate->verification_hash));

        $response->assertStatus(200);
        // Reveals intended credential data
        $response->assertSee('Official Certificate Verified');
        $response->assertSee($this->studentA->name);
        $response->assertSee($this->course->title);
        $response->assertSee('LB-CERT-2026-TESTPRIV');
        $response->assertSee('92.5%');

        // STRICT PRIVACY PROTECTION: Never expose private student data
        $response->assertDontSee($this->studentA->email);
        $response->assertDontSee($this->studentA->phone);
    }

    /**
     * Test public verification displays revoked notice when certificate is revoked.
     */
    public function test_public_verification_handles_revoked_certificate_safely(): void
    {
        $certificate = Certificate::create([
            'certificate_code' => 'LB-CERT-2026-REVOKED1',
            'verification_hash' => hash('sha256', 'secure_hash_revoked_123'),
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'enrollment_id' => $this->enrollmentA->id,
            'issue_date' => now()->toDateString(),
            'is_revoked' => true,
            'revoked_reason' => 'Academic policy breach.',
            'revoked_at' => now(),
        ]);

        $response = $this->get(route('certificates.verify', $certificate->verification_hash));

        $response->assertStatus(200);
        $response->assertSee('Certificate Officially Revoked');
        $response->assertSee('Academic policy breach.');
        $response->assertDontSee('Valid & Authentic');
    }

    /**
     * Test search lookup by certificate code on public verify page.
     */
    public function test_public_search_lookup_by_certificate_code(): void
    {
        $certificate = Certificate::create([
            'certificate_code' => 'LB-CERT-2026-SEARCH01',
            'verification_hash' => hash('sha256', 'secure_hash_search_123'),
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'enrollment_id' => $this->enrollmentA->id,
            'issue_date' => now()->toDateString(),
            'is_revoked' => false,
        ]);

        // Search existing code
        $response = $this->get(route('certificates.search', ['code' => 'LB-CERT-2026-SEARCH01']));
        $response->assertStatus(200);
        $response->assertSee('LB-CERT-2026-SEARCH01');
        $response->assertSee($this->studentA->name);

        // Search non-existent code
        $notFoundResponse = $this->get(route('certificates.search', ['code' => 'LB-CERT-2026-NONEXISTENT']));
        $notFoundResponse->assertStatus(200);
        $notFoundResponse->assertSee('No Credential Found');
    }

    /**
     * Test unauthorised student cannot view another student's certificate document (IDOR).
     */
    public function test_unauthorised_student_cannot_view_another_students_certificate_document(): void
    {
        $certificate = Certificate::create([
            'certificate_code' => 'LB-CERT-2026-IDOR0001',
            'verification_hash' => hash('sha256', 'hash_idor_001'),
            'user_id' => $this->studentA->id,
            'course_id' => $this->course->id,
            'enrollment_id' => $this->enrollmentA->id,
            'issue_date' => now()->toDateString(),
            'is_revoked' => false,
        ]);

        // Student A (owner) can view
        $this->actingAs($this->studentA)->get(route('certificates.show', $certificate))
            ->assertStatus(200);

        // Instructor of the course can view
        $this->actingAs($this->instructor)->get(route('certificates.show', $certificate))
            ->assertStatus(200);

        // Admin can view
        $this->actingAs($this->admin)->get(route('certificates.show', $certificate))
            ->assertStatus(200);

        // Student B (unrelated student) CANNOT view
        $this->actingAs($this->studentB)->get(route('certificates.show', $certificate))
            ->assertStatus(403);
    }

    /**
     * Test only enrolled students can review a course.
     */
    public function test_only_enrolled_students_can_review_courses(): void
    {
        // Student B is NOT enrolled
        $response = $this->actingAs($this->studentB)->post(route('courses.reviews.store', $this->course), [
            'rating' => 5,
            'title' => 'Great Course',
            'comment' => 'This course was incredibly well organized and informative.',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('reviews', 0);
    }

    /**
     * Test enrolled student can post a valid review.
     */
    public function test_enrolled_student_can_post_valid_review(): void
    {
        $response = $this->actingAs($this->studentA)->post(route('courses.reviews.store', $this->course), [
            'rating' => 5,
            'title' => 'Practical & Insightful Masterclass',
            'comment' => 'Learned a tremendous amount about full stack architecture. Highly recommended!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'course_id' => $this->course->id,
            'user_id' => $this->studentA->id,
            'rating' => 5,
            'title' => 'Practical & Insightful Masterclass',
            'is_published' => true,
        ]);
    }

    /**
     * Test review validation rules enforce valid rating range and comment length.
     */
    public function test_review_validation_rules_enforce_valid_ratings_and_content(): void
    {
        // Rating out of bounds (6 stars)
        $invalidRatingResponse = $this->actingAs($this->studentA)->post(route('courses.reviews.store', $this->course), [
            'rating' => 6,
            'comment' => 'Valid length comment here.',
        ]);
        $invalidRatingResponse->assertSessionHasErrors('rating');

        // Comment too short (< 10 chars)
        $shortCommentResponse = $this->actingAs($this->studentA)->post(route('courses.reviews.store', $this->course), [
            'rating' => 4,
            'comment' => 'Too short',
        ]);
        $shortCommentResponse->assertSessionHasErrors('comment');

        $this->assertDatabaseCount('reviews', 0);
    }

    /**
     * Test student cannot submit duplicate reviews for the same course.
     */
    public function test_student_cannot_post_duplicate_reviews_for_same_course(): void
    {
        // First review
        $this->actingAs($this->studentA)->post(route('courses.reviews.store', $this->course), [
            'rating' => 5,
            'title' => 'First Review',
            'comment' => 'Excellent course material and practical assignments.',
        ]);
        $this->assertDatabaseCount('reviews', 1);

        // Second review attempt
        $duplicateResponse = $this->actingAs($this->studentA)->post(route('courses.reviews.store', $this->course), [
            'rating' => 4,
            'title' => 'Second Review Attempt',
            'comment' => 'Attempting to review this course a second time.',
        ]);

        $duplicateResponse->assertStatus(403);
        $this->assertDatabaseCount('reviews', 1);
    }

    /**
     * Test student can update and delete their own review.
     */
    public function test_student_can_update_and_delete_own_review(): void
    {
        $review = Review::create([
            'course_id' => $this->course->id,
            'user_id' => $this->studentA->id,
            'rating' => 4,
            'title' => 'Initial Thought',
            'comment' => 'Good initial lectures, pacing was solid.',
            'is_published' => true,
        ]);

        // Update own review
        $updateResponse = $this->actingAs($this->studentA)->put(route('courses.reviews.update', [$this->course, $review]), [
            'rating' => 5,
            'title' => 'Updated Review: Five Stars!',
            'comment' => 'After completing the final project, I rate this course 5 stars without hesitation.',
        ]);
        $updateResponse->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'title' => 'Updated Review: Five Stars!',
        ]);

        // Delete own review
        $deleteResponse = $this->actingAs($this->studentA)->delete(route('courses.reviews.destroy', [$this->course, $review]));
        $deleteResponse->assertRedirect();

        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }

    /**
     * Test student cannot update or delete another student's review (IDOR).
     */
    public function test_student_cannot_update_or_delete_another_students_review_idor(): void
    {
        $reviewA = Review::create([
            'course_id' => $this->course->id,
            'user_id' => $this->studentA->id,
            'rating' => 5,
            'title' => 'Student A Review',
            'comment' => 'Awesome masterclass with great hands-on examples.',
            'is_published' => true,
        ]);

        // Student B attempts to update Student A's review
        $updateResponse = $this->actingAs($this->studentB)->put(route('courses.reviews.update', [$this->course, $reviewA]), [
            'rating' => 1,
            'title' => 'Tampered Title',
            'comment' => 'Maliciously attempting to alter another student\'s feedback.',
        ]);
        $updateResponse->assertStatus(403);

        // Student B attempts to delete Student A's review
        $deleteResponse = $this->actingAs($this->studentB)->delete(route('courses.reviews.destroy', [$this->course, $reviewA]));
        $deleteResponse->assertStatus(403);

        $this->assertDatabaseHas('reviews', [
            'id' => $reviewA->id,
            'rating' => 5,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test administrator can moderate and hide inappropriate reviews.
     */
    public function test_administrator_can_moderate_inappropriate_reviews(): void
    {
        $review = Review::create([
            'course_id' => $this->course->id,
            'user_id' => $this->studentA->id,
            'rating' => 1,
            'title' => 'Inappropriate Language',
            'comment' => 'Contains offensive comments violating community guidelines.',
            'is_published' => true,
        ]);

        // Regular student cannot moderate
        $unauthorisedResponse = $this->actingAs($this->studentA)->patch(route('courses.reviews.moderate', [$this->course, $review]), [
            'is_published' => 0,
        ]);
        $unauthorisedResponse->assertStatus(403);

        // Administrator moderates and hides the review
        $moderateResponse = $this->actingAs($this->admin)->patch(route('courses.reviews.moderate', [$this->course, $review]), [
            'is_published' => 0,
        ]);
        $moderateResponse->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_published' => false,
        ]);

        // Administrator can re-publish if resolved
        $republishResponse = $this->actingAs($this->admin)->patch(route('courses.reviews.moderate', [$this->course, $review]), [
            'is_published' => 1,
        ]);
        $republishResponse->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_published' => true,
        ]);
    }
}
