<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BatchSchedule;
use App\Models\BatchSession;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\PhysicalLocation;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhysicalLearningTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected User $otherInstructor;

    protected User $student;

    protected User $otherStudent;

    protected Course $course;

    protected CourseDeliveryOption $physicalDeliveryOption;

    protected PhysicalLocation $location;

    protected CourseBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $category = Category::create([
            'name' => 'Information Technology',
            'slug' => 'information-technology',
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
            'title' => 'Full-Stack Web Development Campus Intake',
            'slug' => 'full-stack-web-development-campus-intake',
            'description' => 'In-person classroom training across Sri Lankan campuses.',
            'difficulty_level' => 'intermediate',
            'language' => 'English',
            'duration_hours' => 60,
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->course->instructors()->attach($this->instructor->id, [
            'is_primary' => true,
            'can_manage_content' => true,
            'can_grade' => true,
        ]);

        $this->physicalDeliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'title' => 'Physical Classroom & Lab Access',
            'delivery_mode' => 'physical',
            'price' => 0.00,
            'currency' => 'LKR',
            'is_active' => true,
        ]);

        $this->location = PhysicalLocation::create([
            'name' => 'Colombo City Campus',
            'code' => 'CMB-MAIN',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo 03',
            'address_line_1' => 'No 45 Galle Road',
            'postal_code' => '00300',
            'contact_phone' => '+94 11 234 5678',
            'max_room_capacity' => 40,
            'is_active' => true,
        ]);

        $this->batch = CourseBatch::create([
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'location_id' => $this->location->id,
            'batch_code' => 'FSWD-2026-COL-01',
            'batch_name' => 'Weekend Cohort 01',
            'max_seats' => 25,
            'enrolled_seats' => 0,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addMonths(3)->format('Y-m-d'),
            'status' => CourseBatch::STATUS_OPEN,
        ]);
    }

    public function test_admin_can_view_and_create_physical_locations(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('management.locations.index'));
        $response->assertOk();
        $response->assertSee('Colombo City Campus');

        $storeResponse = $this->post(route('management.locations.store'), [
            'name' => 'Kandy Innovation Hub',
            'code' => 'KDY-01',
            'province' => 'Central',
            'district' => 'Kandy',
            'city' => 'Kandy',
            'address_line_1' => 'No 12 Peradeniya Road',
            'postal_code' => '20000',
            'contact_phone' => '+94 81 222 3344',
            'max_room_capacity' => 35,
            'is_active' => true,
        ]);

        $storeResponse->assertRedirect(route('management.locations.index'));
        $this->assertDatabaseHas('physical_locations', [
            'code' => 'KDY-01',
            'city' => 'Kandy',
            'district' => 'Kandy',
            'province' => 'Central',
            'max_room_capacity' => 35,
        ]);
    }

    public function test_non_admin_cannot_create_or_update_physical_locations(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->post(route('management.locations.store'), [
            'name' => 'Galle Tech Centre',
            'code' => 'GAL-01',
            'province' => 'Southern',
            'district' => 'Galle',
            'city' => 'Galle',
            'address_line_1' => 'Main Street',
            'max_room_capacity' => 30,
            'is_active' => true,
        ]);

        $response->assertForbidden();

        $this->actingAs($this->student);

        $putResponse = $this->put(route('management.locations.update', $this->location), [
            'name' => 'Hacked Campus',
            'code' => 'CMB-MAIN',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo 03',
            'address_line_1' => 'No 45 Galle Road',
            'max_room_capacity' => 30,
            'is_active' => true,
        ]);

        $putResponse->assertForbidden();
    }

    public function test_admin_can_update_physical_location(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('management.locations.update', $this->location), [
            'name' => 'Colombo Flagship Academy',
            'code' => 'CMB-MAIN',
            'province' => 'Western',
            'district' => 'Colombo',
            'city' => 'Colombo 03',
            'address_line_1' => 'No 50 Galle Road',
            'max_room_capacity' => 50,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('management.locations.index'));
        $this->assertDatabaseHas('physical_locations', [
            'id' => $this->location->id,
            'name' => 'Colombo Flagship Academy',
            'max_room_capacity' => 50,
        ]);
    }

    public function test_authorized_instructor_can_create_batch_for_their_course(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->post(route('management.courses.batches.store', $this->course), [
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'location_id' => $this->location->id,
            'batch_code' => 'FSWD-2026-COL-02',
            'batch_name' => 'Weekday Evening Intake',
            'max_seats' => 20,
            'start_date' => now()->addDays(14)->format('Y-m-d'),
            'end_date' => now()->addMonths(4)->format('Y-m-d'),
            'status' => 'upcoming',
        ]);

        $createdBatch = CourseBatch::where('batch_code', 'FSWD-2026-COL-02')->first();
        $this->assertNotNull($createdBatch);
        $response->assertRedirect(route('management.courses.batches.show', [$this->course, $createdBatch]));

        $this->assertDatabaseHas('course_batches', [
            'batch_code' => 'FSWD-2026-COL-02',
            'max_seats' => 20,
            'status' => 'upcoming',
        ]);
    }

    public function test_unauthorized_instructor_cannot_create_batch_for_another_course(): void
    {
        $this->actingAs($this->otherInstructor);

        $response = $this->post(route('management.courses.batches.store', $this->course), [
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'location_id' => $this->location->id,
            'batch_code' => 'FSWD-UNAUTH',
            'batch_name' => 'Unauthorized Batch',
            'max_seats' => 10,
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addMonths(2)->format('Y-m-d'),
            'status' => 'upcoming',
        ]);

        $response->assertForbidden();
    }

    public function test_batch_capacity_limit_and_prevent_overbooking(): void
    {
        // Set batch to full capacity
        $this->batch->update([
            'max_seats' => 2,
            'enrolled_seats' => 2,
        ]);

        $this->actingAs($this->student);

        // Attempt enrollment into full batch
        $response = $this->post(route('enrollments.store', $this->course->slug), [
            'delivery_option_id' => $this->physicalDeliveryOption->id,
            'course_batch_id' => $this->batch->id,
        ]);

        $response->assertSessionHasErrors(['course_batch_id']);
        $this->assertEquals(2, $this->batch->fresh()->enrolled_seats);
    }

    public function test_batch_schedule_slot_can_be_added_and_removed(): void
    {
        $this->actingAs($this->instructor);

        $addResponse = $this->post(route('management.courses.batches.schedules.store', [$this->course, $this->batch]), [
            'day_of_week' => 'Saturday',
            'start_time' => '09:00',
            'end_time' => '13:00',
            'room_number' => 'Lab 3B',
            'notes' => 'Bring personal laptop',
        ]);

        $addResponse->assertRedirect();
        $this->assertDatabaseHas('batch_schedules', [
            'batch_id' => $this->batch->id,
            'day_of_week' => 'Saturday',
            'room_number' => 'Lab 3B',
        ]);

        $this->assertDatabaseHas('batch_schedule_changes', [
            'batch_id' => $this->batch->id,
            'change_type' => 'schedule_slot_added',
        ]);

        $schedule = BatchSchedule::where('batch_id', $this->batch->id)->first();

        // Delete slot
        $deleteResponse = $this->delete(route('management.courses.batches.schedules.destroy', [$this->course, $this->batch, $schedule]));
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('batch_schedules', ['id' => $schedule->id]);
        $this->assertDatabaseHas('batch_schedule_changes', [
            'batch_id' => $this->batch->id,
            'change_type' => 'schedule_slot_removed',
        ]);
    }

    public function test_batch_can_be_cancelled_with_reason_audit_trail(): void
    {
        $this->actingAs($this->instructor);

        $response = $this->post(route('management.courses.batches.cancel', [$this->course, $this->batch]), [
            'reason' => 'Campus facility undergoing planned maintenance.',
        ]);

        $response->assertRedirect(route('management.courses.batches.show', [$this->course, $this->batch]));
        $this->assertEquals(CourseBatch::STATUS_CANCELLED, $this->batch->fresh()->status);

        $this->assertDatabaseHas('batch_schedule_changes', [
            'batch_id' => $this->batch->id,
            'change_type' => 'cancelled',
            'reason' => 'Campus facility undergoing planned maintenance.',
        ]);
    }

    public function test_instructor_can_schedule_session_and_record_attendance(): void
    {
        // Enroll student in batch
        $enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'course_batch_id' => $this->batch->id,
            'enrollment_code' => 'ENR-ATTEND-01',
            'delivery_mode' => 'physical',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 0.00,
            'is_free' => true,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($this->instructor);

        // Schedule session
        $sessionResponse = $this->post(route('management.courses.batches.sessions.store', [$this->course, $this->batch]), [
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '13:00',
            'topic' => 'Module 1: Git & Laravel Architecture',
            'room_number' => 'Lab 3B',
        ]);

        $sessionResponse->assertRedirect();

        $session = BatchSession::where('batch_id', $this->batch->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals(BatchSession::STATUS_SCHEDULED, $session->status);

        // Record attendance
        $attResponse = $this->post(route('management.courses.batches.sessions.attendance.store', [$this->course, $this->batch, $session]), [
            'attendances' => [
                [
                    'user_id' => $this->student->id,
                    'status' => 'present',
                    'remarks' => 'Active participation in lab',
                ],
            ],
        ]);

        $attResponse->assertRedirect(route('management.courses.batches.show', [$this->course, $this->batch]));

        $this->assertEquals(BatchSession::STATUS_COMPLETED, $session->fresh()->status);
        $this->assertDatabaseHas('attendances', [
            'batch_session_id' => $session->id,
            'enrollment_id' => $enrollment->id,
            'user_id' => $this->student->id,
            'status' => 'present',
            'remarks' => 'Active participation in lab',
            'recorded_by_user_id' => $this->instructor->id,
        ]);

        $this->assertEquals(100.0, $enrollment->attendanceRate());
    }

    public function test_unauthorized_instructor_cannot_record_attendance(): void
    {
        $session = BatchSession::create([
            'batch_id' => $this->batch->id,
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '13:00',
            'topic' => 'PHP Testing',
            'status' => BatchSession::STATUS_SCHEDULED,
        ]);

        $this->actingAs($this->otherInstructor);

        $response = $this->post(route('management.courses.batches.sessions.attendance.store', [$this->course, $this->batch, $session]), [
            'attendances' => [
                [
                    'user_id' => $this->student->id,
                    'status' => 'present',
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_enrolled_student_can_view_own_batch_schedule_and_attendance(): void
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->physicalDeliveryOption->id,
            'course_batch_id' => $this->batch->id,
            'enrollment_code' => 'ENR-STUDENT-VIEW',
            'delivery_mode' => 'physical',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 0.00,
            'is_free' => true,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($this->student);

        $response = $this->get(route('student.batches.show', $this->batch));
        $response->assertOk();
        $response->assertSee('Weekend Cohort 01');
        $response->assertSee('Colombo City Campus');
        $response->assertSee('No 45 Galle Road');
    }

    public function test_unenrolled_student_cannot_view_batch_schedule(): void
    {
        $this->actingAs($this->otherStudent);

        $response = $this->get(route('student.batches.show', $this->batch));
        $response->assertForbidden();
    }
}
