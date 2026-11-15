<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonResource;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLearningTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $otherStudent;

    protected User $instructor;

    protected User $unrelatedInstructor;

    protected User $admin;

    protected Course $course;

    protected CourseSection $section1;

    protected CourseSection $section2;

    protected Lesson $lesson1;

    protected Lesson $lesson2;

    protected Lesson $unpublishedLesson;

    protected Course $otherCourse;

    protected Lesson $otherCourseLesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $category = Category::firstOrCreate(
            ['slug' => 'cloud-computing'],
            ['name' => 'Cloud Computing', 'sort_order' => 1]
        );

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->unrelatedInstructor = User::factory()->create();
        $this->unrelatedInstructor->assignRole('instructor');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->otherStudent = User::factory()->create();
        $this->otherStudent->assignRole('student');

        // Main course setup
        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Mastering Microservices with Go and Laravel',
            'slug' => 'mastering-microservices-go-laravel',
            'subtitle' => 'Build high-scale distributed systems',
            'description' => 'Comprehensive architecture course for senior developers.',
            'difficulty_level' => 'advanced',
            'language' => 'English',
            'duration_hours' => 20,
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->course->instructors()->attach($this->instructor->id, [
            'is_primary' => true,
            'can_manage_content' => true,
            'can_grade' => true,
        ]);

        $deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'delivery_mode' => 'online',
            'title' => 'Full Online Self-Paced Access',
            'is_free' => true,
            'price' => 0.00,
            'currency' => 'LKR',
            'is_active' => true,
        ]);

        // Section 1 with 2 published lessons
        $this->section1 = CourseSection::create([
            'course_id' => $this->course->id,
            'title' => 'Module 1: Architecture Foundations',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->lesson1 = Lesson::create([
            'section_id' => $this->section1->id,
            'title' => '1.1 Monolith vs Distributed Microservices',
            'slug' => 'monolith-vs-distributed-microservices',
            'type' => 'video',
            'content' => 'In-depth explanation of service boundaries and decoupling.',
            'duration_minutes' => 15,
            'sort_order' => 1,
            'is_preview' => false,
            'is_published' => true,
        ]);

        $this->lesson2 = Lesson::create([
            'section_id' => $this->section1->id,
            'title' => '1.2 Domain Driven Design & Bounded Contexts',
            'slug' => 'domain-driven-design-bounded-contexts',
            'type' => 'video',
            'content' => 'Mapping domain models and event storms.',
            'duration_minutes' => 22,
            'sort_order' => 2,
            'is_preview' => false,
            'is_published' => true,
        ]);

        // Section 2 with an unpublished draft lesson
        $this->section2 = CourseSection::create([
            'course_id' => $this->course->id,
            'title' => 'Module 2: Message Brokers & Event Driven Architecture',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $this->unpublishedLesson = Lesson::create([
            'section_id' => $this->section2->id,
            'title' => '2.1 Kafka Streams Deep Dive',
            'slug' => 'kafka-streams-deep-dive',
            'type' => 'video',
            'content' => 'Work in progress draft lesson.',
            'duration_minutes' => 30,
            'sort_order' => 1,
            'is_preview' => false,
            'is_published' => false, // Unpublished
        ]);

        // Second course for IDOR and cross-contamination tests
        $this->otherCourse = Course::create([
            'category_id' => $category->id,
            'title' => 'Python for Artificial Intelligence',
            'slug' => 'python-artificial-intelligence',
            'description' => 'Complete Python machine learning roadmap.',
            'status' => Course::STATUS_PUBLISHED,
            'created_by_user_id' => $this->unrelatedInstructor->id,
        ]);

        $otherSection = CourseSection::create([
            'course_id' => $this->otherCourse->id,
            'title' => 'Module 1: Tensors',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->otherCourseLesson = Lesson::create([
            'section_id' => $otherSection->id,
            'title' => 'PyTorch Fundamentals',
            'type' => 'video',
            'duration_minutes' => 18,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        // Enroll $this->student into $this->course
        Enrollment::create([
            'enrollment_code' => 'ENR-ACT-001',
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $deliveryOption->id,
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'amount_paid' => 0.00,
            'currency' => 'LKR',
            'enrolled_at' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('learning.index', $this->course));
        $response->assertRedirect(route('login'));

        $lessonResponse = $this->get(route('learning.show', [$this->course, $this->lesson1]));
        $lessonResponse->assertRedirect(route('login'));
    }

    public function test_non_enrolled_student_cannot_access_learning_screen(): void
    {
        $response = $this->actingAs($this->otherStudent)
            ->get(route('learning.index', $this->course));

        $response->assertForbidden();

        $lessonResponse = $this->actingAs($this->otherStudent)
            ->get(route('learning.show', [$this->course, $this->lesson1]));

        $lessonResponse->assertForbidden();
    }

    public function test_pending_payment_student_cannot_access_learning_screen(): void
    {
        // Change student enrollment to pending payment
        Enrollment::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->update(['status' => Enrollment::STATUS_PENDING_PAYMENT]);

        $response = $this->actingAs($this->student)
            ->get(route('learning.index', $this->course));

        $response->assertForbidden();
    }

    public function test_enrolled_student_is_redirected_to_first_accessible_lesson(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('learning.index', $this->course));

        $response->assertRedirect(route('learning.show', [$this->course, $this->lesson1]));
    }

    public function test_enrolled_student_can_view_learning_interface(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('learning.show', [$this->course, $this->lesson1]));

        $response->assertOk();
        $response->assertSee('1.1 Monolith vs Distributed Microservices');
        $response->assertSee('Mastering Microservices with Go and Laravel');
        $response->assertSee('Course Curriculum');
        $response->assertSee('Overview');
        $response->assertSee('My Notes');
        $response->assertSee('Resources');
    }

    public function test_admin_and_assigned_instructor_can_access_learning_without_enrollment(): void
    {
        // Admin
        $adminResponse = $this->actingAs($this->admin)
            ->get(route('learning.show', [$this->course, $this->lesson1]));
        $adminResponse->assertOk();

        // Assigned Instructor
        $instructorResponse = $this->actingAs($this->instructor)
            ->get(route('learning.show', [$this->course, $this->lesson1]));
        $instructorResponse->assertOk();

        // Unassigned Instructor
        $unrelatedResponse = $this->actingAs($this->unrelatedInstructor)
            ->get(route('learning.show', [$this->course, $this->lesson1]));
        $unrelatedResponse->assertForbidden();
    }

    public function test_student_cannot_access_unpublished_lesson(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('learning.show', [$this->course, $this->unpublishedLesson]));

        $response->assertForbidden();

        // However, the instructor CAN preview the unpublished lesson
        $instructorResponse = $this->actingAs($this->instructor)
            ->get(route('learning.show', [$this->course, $this->unpublishedLesson]));

        $instructorResponse->assertOk();
        $instructorResponse->assertSee('Unpublished Preview');
    }

    public function test_cross_course_idor_is_prevented_when_passing_foreign_lesson_id(): void
    {
        // Attempting to access other course's lesson under this course route
        $response = $this->actingAs($this->student)
            ->get(route('learning.show', [$this->course, $this->otherCourseLesson]));

        $response->assertNotFound();
    }

    public function test_student_can_toggle_lesson_completion_and_progress_is_updated(): void
    {
        // Initial progress: 0%
        $this->assertEquals(0, $this->course->calculateProgressPercentageFor($this->student));

        // Mark lesson 1 as complete via JSON POST
        $response = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson1]), [
                'is_completed' => 1,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_completed' => true,
            'course_progress_percentage' => 50, // 1 of 2 published lessons completed
        ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'is_completed' => true,
        ]);

        // Complete lesson 2
        $response2 = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson2]), [
                'is_completed' => 1,
            ]);

        $response2->assertOk();
        $response2->assertJson([
            'success' => true,
            'is_completed' => true,
            'course_progress_percentage' => 100, // 2 of 2 published lessons completed
        ]);

        // Toggle lesson 1 back to incomplete
        $responseToggle = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson1]), [
                'is_completed' => 0,
            ]);

        $responseToggle->assertOk();
        $responseToggle->assertJson([
            'success' => true,
            'is_completed' => false,
            'course_progress_percentage' => 50,
        ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    public function test_student_can_save_personal_notes(): void
    {
        $noteText = 'Kafka partition keys dictate which consumer thread processes the stream.';

        $response = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson1]), [
                'notes' => $noteText,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'notes' => $noteText,
        ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson1->id,
            'notes' => $noteText,
        ]);
    }

    public function test_cannot_update_progress_for_foreign_lesson_or_unpublished_lesson(): void
    {
        // Foreign lesson from different course
        $foreignResponse = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->otherCourseLesson]), [
                'is_completed' => 1,
            ]);

        $foreignResponse->assertNotFound();

        // Unpublished lesson by student
        $unpublishedResponse = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->unpublishedLesson]), [
                'is_completed' => 1,
            ]);

        $unpublishedResponse->assertForbidden();
    }

    public function test_student_can_download_lesson_resource_with_external_link(): void
    {
        $resource = LessonResource::create([
            'lesson_id' => $this->lesson1->id,
            'title' => 'Architecture Diagram Slides',
            'file_name' => 'slides.pdf',
            'file_type' => 'pdf',
            'external_url' => 'https://example.com/slides.pdf',
            'is_downloadable' => true,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('learning.resources.download', [$this->course, $this->lesson1, $resource]));

        $response->assertRedirect('https://example.com/slides.pdf');

        // Non-enrolled student gets 403
        $unauthResponse = $this->actingAs($this->otherStudent)
            ->get(route('learning.resources.download', [$this->course, $this->lesson1, $resource]));

        $unauthResponse->assertForbidden();
    }

    public function test_learning_theater_displays_certificate_action_when_completed_and_eligible(): void
    {
        // 1. Initially student has not completed lessons, so isCertificateEligible is false
        $initialResponse = $this->actingAs($this->student)
            ->get(route('learning.show', [$this->course, $this->lesson1]));

        $initialResponse->assertOk();
        $initialResponse->assertViewHas('isCertificateEligible', false);

        // 2. Complete all accessible lessons for this course
        $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson1]), [
                'is_completed' => 1,
            ])->assertOk();

        $updateResponse = $this->actingAs($this->student)
            ->postJson(route('learning.progress', [$this->course, $this->lesson2]), [
                'is_completed' => 1,
            ]);

        $updateResponse->assertOk();
        $updateResponse->assertJson([
            'is_certificate_eligible' => true,
        ]);

        // 3. Visiting the learning show page now renders the certificate button
        $completedResponse = $this->actingAs($this->student)
            ->get(route('learning.show', [$this->course, $this->lesson1]));

        $completedResponse->assertOk();
        $completedResponse->assertViewHas('isCertificateEligible', true);
        $completedResponse->assertSee('Get Certificate');
        $completedResponse->assertSee(route('certificates.eligibility', $this->course));
    }
}
