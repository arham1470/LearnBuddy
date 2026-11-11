<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveClassTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected User $otherInstructor;

    protected User $student;

    protected User $otherStudent;

    protected Course $course;

    protected CourseDeliveryOption $deliveryOption;

    protected LiveClass $liveClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
        ]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->otherInstructor = User::factory()->create();
        $this->otherInstructor->assignRole('instructor');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->otherStudent = User::factory()->create();
        $this->otherStudent->assignRole('student');

        $this->course = Course::create([
            'category_id' => $category->id,
            'title' => 'Full-Stack Modern Web Engineering',
            'slug' => 'full-stack-modern-web-engineering',
            'description' => 'Interactive course with recorded lectures and live interactive sessions.',
            'difficulty_level' => 'intermediate',
            'language' => 'English',
            'duration_hours' => 40,
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->course->instructors()->attach($this->instructor->id, [
            'is_primary' => true,
            'can_manage_content' => true,
            'can_grade' => true,
        ]);

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'title' => 'Full Curriculum & Live Access',
            'delivery_mode' => 'online',
            'price' => 0.00,
            'currency' => 'LKR',
            'is_active' => true,
            'includes_live_classes' => true,
        ]);

        // Enroll primary student
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-LIVE-01',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 0.00,
            'is_free' => true,
            'enrolled_at' => now(),
        ]);

        // Default upcoming live class (scheduled 2 hours from now)
        $this->liveClass = LiveClass::create([
            'course_id' => $this->course->id,
            'instructor_id' => $this->instructor->id,
            'title' => 'Live Architecture Review & Code Refactoring',
            'description' => 'Real-time interactive session reviewing student repository pull requests.',
            'scheduled_start_time' => now()->addHours(2),
            'scheduled_end_time' => now()->addHours(4),
            'provider' => 'jitsi',
            'meeting_identifier' => 'learnbuddy-test-meeting-room-1',
            'status' => LiveClass::STATUS_SCHEDULED,
            'access_permission' => LiveClass::ACCESS_ENROLLED_STUDENTS,
        ]);
    }

    public function test_authorized_instructor_can_view_and_schedule_live_class(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->get(route('management.courses.live-classes.index', $this->course));
        $response->assertOk();
        $response->assertSee('Live Architecture Review');

        $createResponse = $this->get(route('management.courses.live-classes.create', $this->course));
        $createResponse->assertOk();

        $storeResponse = $this->post(route('management.courses.live-classes.store', $this->course), [
            'title' => 'Live Database Optimization Workshop',
            'description' => 'Indexing, EXPLAIN plans, and caching in MySQL.',
            'instructor_id' => $this->instructor->id,
            'scheduled_start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'scheduled_end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'access_permission' => 'enrolled_students',
        ]);

        $created = LiveClass::where('title', 'Live Database Optimization Workshop')->first();
        $this->assertNotNull($created);
        $this->assertEquals('jitsi', $created->provider);
        $this->assertNotEmpty($created->meeting_identifier);

        $storeResponse->assertRedirect(route('management.courses.live-classes.show', [$this->course, $created]));
    }

    public function test_unauthorized_instructor_cannot_schedule_live_class_for_another_course(): void
    {
        $this->actingAs($this->otherInstructor);

        $response = $this->post(route('management.courses.live-classes.store', $this->course), [
            'title' => 'Unauthorized Session',
            'instructor_id' => $this->otherInstructor->id,
            'scheduled_start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'scheduled_end_time' => now()->addDays(1)->addHours(1)->format('Y-m-d H:i:s'),
            'access_permission' => 'enrolled_students',
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_access_management_endpoints(): void
    {
        $this->actingAs($this->student);

        $indexResponse = $this->get(route('management.courses.live-classes.index', $this->course));
        $indexResponse->assertForbidden();

        $storeResponse = $this->post(route('management.courses.live-classes.store', $this->course), [
            'title' => 'Hacked Session',
            'instructor_id' => $this->instructor->id,
            'scheduled_start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'scheduled_end_time' => now()->addDays(1)->addHours(1)->format('Y-m-d H:i:s'),
            'access_permission' => 'enrolled_students',
        ]);

        $storeResponse->assertForbidden();
    }

    public function test_authorized_instructor_can_reschedule_live_class(): void
    {
        $this->actingAs($this->instructor);

        $newStart = now()->addDays(3)->setHour(14)->setMinute(0);
        $newEnd = now()->addDays(3)->setHour(16)->setMinute(0);

        $response = $this->put(route('management.courses.live-classes.update', [$this->course, $this->liveClass]), [
            'title' => 'Rescheduled: Architecture Review',
            'instructor_id' => $this->instructor->id,
            'scheduled_start_time' => $newStart->format('Y-m-d H:i:s'),
            'scheduled_end_time' => $newEnd->format('Y-m-d H:i:s'),
            'access_permission' => 'enrolled_students',
            'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('management.courses.live-classes.show', [$this->course, $this->liveClass]));

        $this->assertDatabaseHas('live_classes', [
            'id' => $this->liveClass->id,
            'title' => 'Rescheduled: Architecture Review',
        ]);
    }

    public function test_authorized_instructor_can_cancel_live_class_with_reason(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->post(route('management.courses.live-classes.cancel', [$this->course, $this->liveClass]), [
            'cancellation_reason' => 'Instructor scheduled for emergency campus inspection.',
        ]);

        $response->assertRedirect(route('management.courses.live-classes.show', [$this->course, $this->liveClass]));

        $this->assertEquals(LiveClass::STATUS_CANCELLED, $this->liveClass->fresh()->status);
        $this->assertEquals('Instructor scheduled for emergency campus inspection.', $this->liveClass->fresh()->cancellation_reason);
    }

    public function test_reminder_hook_can_be_triggered_by_instructor(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->post(route('management.courses.live-classes.reminder', [$this->course, $this->liveClass]));
        $response->assertRedirect();

        $this->assertNotNull($this->liveClass->fresh()->reminder_sent_at);
    }

    public function test_enrolled_student_can_view_live_classes_list_and_briefing(): void
    {
        $this->actingAs($this->student);

        $indexResponse = $this->get(route('courses.live-classes.index', $this->course->slug));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Live Architecture Review & Code Refactoring');

        $showResponse = $this->get(route('courses.live-classes.show', [$this->course->slug, $this->liveClass]));
        $showResponse->assertOk();
        $showResponse->assertSee('Live Architecture Review & Code Refactoring');
    }

    public function test_unenrolled_student_cannot_view_or_join_live_class(): void
    {
        $this->actingAs($this->otherStudent);

        $showResponse = $this->get(route('courses.live-classes.show', [$this->course->slug, $this->liveClass]));
        $showResponse->assertForbidden();

        $joinResponse = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $joinResponse->assertForbidden();
    }

    public function test_enrolled_student_cannot_join_before_time_window(): void
    {
        // Session is scheduled 2 hours in the future (outside 15-min join buffer)
        $this->actingAs($this->student);

        $response = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $response->assertForbidden();
    }

    public function test_enrolled_student_can_join_within_time_window(): void
    {
        // Set scheduled start time to 5 minutes from now (inside the 15-minute join window)
        $this->liveClass->update([
            'scheduled_start_time' => now()->addMinutes(5),
            'scheduled_end_time' => now()->addHours(2),
            'status' => LiveClass::STATUS_SCHEDULED,
        ]);

        $this->actingAs($this->student);

        $response = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $response->assertOk();
        $response->assertViewIs('live_classes.room');
        $response->assertSee('Student Participant');
    }

    public function test_student_cannot_join_cancelled_or_completed_session(): void
    {
        $this->liveClass->update([
            'scheduled_start_time' => now()->subMinutes(10),
            'status' => LiveClass::STATUS_CANCELLED,
            'cancellation_reason' => 'Technical issues with internet backbone.',
        ]);

        $this->actingAs($this->student);

        $response = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $response->assertForbidden();

        $this->liveClass->update([
            'status' => LiveClass::STATUS_COMPLETED,
        ]);

        $completedResponse = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $completedResponse->assertForbidden();
    }

    public function test_unconfigured_provider_shows_clear_unconfigured_state_instead_of_fake_meeting(): void
    {
        // Meeting is active/joinable
        $this->liveClass->update([
            'scheduled_start_time' => now()->subMinutes(5),
            'scheduled_end_time' => now()->addHour(),
            'status' => LiveClass::STATUS_LIVE,
        ]);

        $this->actingAs($this->student);

        $response = $this->get(route('courses.live-classes.join', [$this->course->slug, $this->liveClass]));
        $response->assertOk();
        $response->assertSee('Live Meeting Provider Not Configured');
        $response->assertSee('8x8 JaaS');
        $response->assertDontSee('fake-working-meeting');
    }

    public function test_admin_can_manage_all_live_sessions(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('management.courses.live-classes.show', [$this->course, $this->liveClass]));
        $response->assertOk();

        $cancelResponse = $this->post(route('management.courses.live-classes.cancel', [$this->course, $this->liveClass]), [
            'cancellation_reason' => 'Administrative schedule adjustment.',
        ]);

        $cancelResponse->assertRedirect();
        $this->assertEquals(LiveClass::STATUS_CANCELLED, $this->liveClass->fresh()->status);
    }
}
