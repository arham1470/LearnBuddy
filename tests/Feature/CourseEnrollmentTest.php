<?php

namespace Tests\Feature;

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

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;

    protected User $studentB;

    protected User $instructor;

    protected Course $publishedFreeCourse;

    protected Course $publishedPaidCourse;

    protected CourseDeliveryOption $freeOnlineOption;

    protected CourseDeliveryOption $paidOnlineOption;

    protected CourseDeliveryOption $paidPhysicalOption;

    protected CourseBatch $openBatch;

    protected CourseBatch $fullBatch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $category = Category::firstOrCreate(
            ['slug' => 'software-engineering'],
            ['name' => 'Software Engineering', 'sort_order' => 1]
        );

        $location = PhysicalLocation::firstOrCreate(
            ['code' => 'COL-LAB-TEST'],
            [
                'name' => 'Colombo Campus Lab',
                'province' => 'Western',
                'district' => 'Colombo',
                'city' => 'Colombo 03',
                'address_line_1' => 'Galle Road',
                'max_capacity' => 30,
            ]
        );

        $this->instructor = User::firstOrCreate(
            ['email' => 'instructor.test@learnbuddy.lk'],
            ['name' => 'Dilshan Instructor', 'password' => bcrypt('Password123!')]
        );
        $this->instructor->assignRole('instructor');

        $this->studentA = User::firstOrCreate(
            ['email' => 'student.a@learnbuddy.lk'],
            ['name' => 'Student Alpha', 'password' => bcrypt('Password123!')]
        );
        $this->studentA->assignRole('student');

        $this->studentB = User::firstOrCreate(
            ['email' => 'student.b@learnbuddy.lk'],
            ['name' => 'Student Beta', 'password' => bcrypt('Password123!')]
        );
        $this->studentB->assignRole('student');

        // 1. Free Course
        $this->publishedFreeCourse = Course::create([
            'category_id' => $category->id,
            'title' => 'Free Introduction to Programming',
            'slug' => 'free-intro-programming',
            'description' => 'Starter coding lessons for students.',
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->freeOnlineOption = CourseDeliveryOption::create([
            'course_id' => $this->publishedFreeCourse->id,
            'delivery_mode' => 'online',
            'title' => 'Free Online Access',
            'price' => 0.00,
            'currency' => 'LKR',
            'is_free' => true,
            'is_active' => true,
        ]);

        // 2. Paid Course with Online & Physical options
        $this->publishedPaidCourse = Course::create([
            'category_id' => $category->id,
            'title' => 'Advanced Full-Stack Engineering',
            'slug' => 'advanced-fullstack-engineering',
            'description' => 'Comprehensive masterclass on Laravel and cloud databases.',
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
            'created_by_user_id' => $this->instructor->id,
        ]);

        $this->paidOnlineOption = CourseDeliveryOption::create([
            'course_id' => $this->publishedPaidCourse->id,
            'delivery_mode' => 'online',
            'title' => 'Paid Online Stream',
            'price' => 8500.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
        ]);

        $this->paidPhysicalOption = CourseDeliveryOption::create([
            'course_id' => $this->publishedPaidCourse->id,
            'delivery_mode' => 'physical',
            'title' => 'In-Person Colombo Lab',
            'price' => 25000.00,
            'currency' => 'LKR',
            'is_free' => false,
            'is_active' => true,
            'max_capacity' => 20,
        ]);

        $this->openBatch = CourseBatch::create([
            'course_delivery_option_id' => $this->paidPhysicalOption->id,
            'location_id' => $location->id,
            'batch_code' => 'BAT-OPEN-01',
            'batch_name' => 'Colombo Weekend Intake',
            'max_seats' => 20,
            'enrolled_seats' => 5,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(60),
            'status' => 'open_for_enrollment',
        ]);

        $this->fullBatch = CourseBatch::create([
            'course_delivery_option_id' => $this->paidPhysicalOption->id,
            'location_id' => $location->id,
            'batch_code' => 'BAT-FULL-01',
            'batch_name' => 'Colombo Evening Lab (Sold Out)',
            'max_seats' => 15,
            'enrolled_seats' => 15,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(60),
            'status' => 'open_for_enrollment',
        ]);
    }

    public function test_guest_is_redirected_to_login_from_enrollment_and_dashboard(): void
    {
        $this->get(route('enrollments.create', $this->publishedFreeCourse))
            ->assertRedirect(route('login'));

        $this->post(route('enrollments.store', $this->publishedFreeCourse), [])
            ->assertRedirect(route('login'));

        $this->get(route('student.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_student_can_view_checkout_preview_screen(): void
    {
        $response = $this->actingAs($this->studentA)
            ->get(route('enrollments.create', $this->publishedFreeCourse));

        $response->assertStatus(200);
        $response->assertSee($this->publishedFreeCourse->title);
        $response->assertSee('Free Online Access');
        $response->assertSee($this->studentA->name);
    }

    public function test_free_enrollment_activates_immediately(): void
    {
        $response = $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedFreeCourse), [
                'delivery_option_id' => $this->freeOnlineOption->id,
            ]);

        $enrollment = Enrollment::where('user_id', $this->studentA->id)
            ->where('course_id', $this->publishedFreeCourse->id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals(Enrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertTrue($enrollment->is_free);
        $this->assertEquals('0.00', $enrollment->price_paid);
        $this->assertNotNull($enrollment->enrolled_at);

        $response->assertRedirect(route('enrollments.confirmation', $enrollment));
        $response->assertSessionHas('success');
    }

    public function test_paid_enrollment_creates_pending_payment_state(): void
    {
        $response = $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedPaidCourse), [
                'delivery_option_id' => $this->paidOnlineOption->id,
            ]);

        $enrollment = Enrollment::where('user_id', $this->studentA->id)
            ->where('course_id', $this->publishedPaidCourse->id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals(Enrollment::STATUS_PENDING_PAYMENT, $enrollment->status);
        $this->assertFalse($enrollment->is_free);
        $this->assertEquals('8500.00', $enrollment->price_paid);

        $response->assertRedirect(route('enrollments.confirmation', $enrollment));
    }

    public function test_duplicate_enrollment_in_same_course_is_rejected(): void
    {
        // First enrollment
        $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedFreeCourse), [
                'delivery_option_id' => $this->freeOnlineOption->id,
            ]);

        // Attempting second enrollment
        $response = $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedFreeCourse), [
                'delivery_option_id' => $this->freeOnlineOption->id,
            ]);

        $response->assertSessionHasErrors('delivery_option_id');
        $this->assertEquals(1, Enrollment::where('user_id', $this->studentA->id)->where('course_id', $this->publishedFreeCourse->id)->count());
    }

    public function test_physical_batch_enrollment_reserves_seat_and_increments_count(): void
    {
        $initialSeats = $this->openBatch->enrolled_seats;

        $response = $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedPaidCourse), [
                'delivery_option_id' => $this->paidPhysicalOption->id,
                'course_batch_id' => $this->openBatch->id,
            ]);

        $this->openBatch->refresh();
        $this->assertEquals($initialSeats + 1, $this->openBatch->enrolled_seats);

        $enrollment = Enrollment::where('user_id', $this->studentA->id)->where('course_id', $this->publishedPaidCourse->id)->first();
        $this->assertEquals($this->openBatch->id, $enrollment->course_batch_id);
        $this->assertEquals('physical', $enrollment->delivery_mode);

        $response->assertRedirect(route('enrollments.confirmation', $enrollment));
    }

    public function test_full_physical_batch_enrollment_is_rejected(): void
    {
        $response = $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedPaidCourse), [
                'delivery_option_id' => $this->paidPhysicalOption->id,
                'course_batch_id' => $this->fullBatch->id,
            ]);

        $response->assertSessionHasErrors('course_batch_id');
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $this->studentA->id,
            'course_id' => $this->publishedPaidCourse->id,
        ]);
    }

    public function test_student_cannot_view_another_students_enrollment_confirmation(): void
    {
        // Student A enrolls
        $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedFreeCourse), [
                'delivery_option_id' => $this->freeOnlineOption->id,
            ]);

        $enrollmentA = Enrollment::where('user_id', $this->studentA->id)->first();

        // Student A can view their own confirmation
        $this->actingAs($this->studentA)
            ->get(route('enrollments.confirmation', $enrollmentA))
            ->assertStatus(200)
            ->assertSee($enrollmentA->enrollment_code);

        // Student B attempting to view Student A's confirmation receives 403 Forbidden
        $this->actingAs($this->studentB)
            ->get(route('enrollments.confirmation', $enrollmentA))
            ->assertStatus(403);
    }

    public function test_student_dashboard_displays_enrolled_courses_and_batch_schedules(): void
    {
        // Enroll in free course (Active)
        $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedFreeCourse), [
                'delivery_option_id' => $this->freeOnlineOption->id,
            ]);

        // Enroll in paid physical course (Pending Payment)
        $this->actingAs($this->studentA)
            ->post(route('enrollments.store', $this->publishedPaidCourse), [
                'delivery_option_id' => $this->paidPhysicalOption->id,
                'course_batch_id' => $this->openBatch->id,
            ]);

        $response = $this->actingAs($this->studentA)
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($this->studentA->name);
        $response->assertSee($this->publishedFreeCourse->title);
        $response->assertSee($this->publishedPaidCourse->title);
        $response->assertSee('Pending Payment');
        $response->assertSee('No live interactive classes scheduled');
    }
}
