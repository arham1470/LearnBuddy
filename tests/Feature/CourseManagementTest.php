<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\PhysicalLocation;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructorA;

    protected User $instructorB;

    protected User $student;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->admin()->create(['name' => 'Admin User']);
        $this->instructorA = User::factory()->instructor()->create(['name' => 'Instructor Alpha']);
        $this->instructorB = User::factory()->instructor()->create(['name' => 'Instructor Beta']);
        $this->student = User::factory()->student()->create(['name' => 'Student User']);

        $this->category = Category::factory()->create(['name' => 'Computer Science', 'slug' => 'computer-science']);
    }

    public function test_unauthenticated_user_cannot_access_course_management(): void
    {
        $response = $this->get('/management/courses');
        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_course_management(): void
    {
        $response = $this->actingAs($this->student)->get('/management/courses');
        $response->assertStatus(403);
    }

    public function test_administrator_can_view_course_management_index(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'title' => 'Advanced Cloud Security',
        ]);

        $response = $this->actingAs($this->admin)->get('/management/courses');

        $response->assertStatus(200);
        $response->assertSee('Courses');
        $response->assertSee('Advanced Cloud Security');
    }

    public function test_instructor_only_sees_their_own_courses_in_management_index(): void
    {
        // Course assigned to Instructor A
        $courseA = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'title' => 'Course by Instructor Alpha',
        ]);
        $courseA->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        // Course assigned to Instructor B
        $courseB = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorB->id,
            'title' => 'Course by Instructor Beta',
        ]);
        $courseB->instructors()->attach($this->instructorB->id, ['is_primary' => true]);

        // Instructor A views index
        $responseA = $this->actingAs($this->instructorA)->get('/management/courses');
        $responseA->assertStatus(200);
        $responseA->assertSee('Course by Instructor Alpha');
        $responseA->assertDontSee('Course by Instructor Beta');

        // Admin views index: sees both
        $responseAdmin = $this->actingAs($this->admin)->get('/management/courses');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Course by Instructor Alpha');
        $responseAdmin->assertSee('Course by Instructor Beta');
    }

    public function test_instructor_can_create_a_course_draft_and_is_assigned_as_primary_instructor(): void
    {
        Storage::fake('public');

        $thumbnail = UploadedFile::fake()->image('course_thumb.jpg', 640, 360);

        $response = $this->actingAs($this->instructorA)->post('/management/courses', [
            'category_id' => $this->category->id,
            'title' => 'Modern PHP Architecture',
            'subtitle' => 'From fundamentals to enterprise patterns',
            'description' => 'Comprehensive syllabus on modern design patterns in Sri Lanka.',
            'difficulty_level' => 'intermediate',
            'language' => 'English',
            'duration_hours' => 20.0,
            'thumbnail' => $thumbnail,
            'learning_outcomes' => ['Understand clean code', 'Design robust databases'],
            'prerequisites' => ['Basic PHP'],
            'delivery_modes' => ['online'],
            'online_price' => 8500.00,
        ]);

        $course = Course::where('title', 'Modern PHP Architecture')->first();
        $this->assertNotNull($course);
        $response->assertRedirect(route('management.courses.show', $course));

        $this->assertEquals(Course::STATUS_DRAFT, $course->status);
        $this->assertEquals($this->instructorA->id, $course->created_by_user_id);
        $this->assertTrue($course->instructors->contains('id', $this->instructorA->id));
        $this->assertTrue($course->isPrimaryInstructor($this->instructorA));

        // Delivery option verified
        $this->assertDatabaseHas('course_delivery_options', [
            'course_id' => $course->id,
            'delivery_mode' => 'online',
            'price' => 8500.00,
            'currency' => 'LKR',
        ]);
    }

    public function test_administrator_can_create_course_with_multiple_delivery_options_and_batch(): void
    {
        $location = PhysicalLocation::factory()->create(['name' => 'Colombo Campus Lab 2']);

        $response = $this->actingAs($this->admin)->post('/management/courses', [
            'category_id' => $this->category->id,
            'title' => 'Enterprise Cloud Systems',
            'subtitle' => 'Offered Online and in Colombo Campus',
            'description' => 'Detailed curriculum covering both self-paced modules and hands-on laboratory sessions.',
            'difficulty_level' => 'advanced',
            'language' => 'English',
            'duration_hours' => 40.0,
            'instructor_ids' => [$this->instructorA->id, $this->instructorB->id],
            'delivery_modes' => ['online', 'physical'],
            'online_price' => 10000.00,
            'physical_price' => 25000.00,
            'physical_max_capacity' => 20,
            'physical_location_id' => $location->id,
        ]);

        $course = Course::where('title', 'Enterprise Cloud Systems')->first();
        $this->assertNotNull($course);
        $response->assertRedirect(route('management.courses.show', $course));

        // Verify multiple delivery options on the same single course record
        $this->assertCount(2, $course->deliveryOptions);
        $this->assertDatabaseHas('course_delivery_options', [
            'course_id' => $course->id,
            'delivery_mode' => 'online',
            'price' => 10000.00,
        ]);
        $this->assertDatabaseHas('course_delivery_options', [
            'course_id' => $course->id,
            'delivery_mode' => 'physical',
            'price' => 25000.00,
            'max_capacity' => 20,
        ]);

        // Verify batch was initialized
        $physicalOption = $course->deliveryOptions()->where('delivery_mode', 'physical')->first();
        $this->assertDatabaseHas('course_batches', [
            'course_delivery_option_id' => $physicalOption->id,
            'location_id' => $location->id,
            'max_seats' => 20,
        ]);
    }

    public function test_course_creation_validates_required_fields_and_safe_image_limits(): void
    {
        Storage::fake('public');

        // File too large (> 2048 KB) and invalid MIME
        $oversizedFile = UploadedFile::fake()->create('document.pdf', 3000, 'application/pdf');

        $response = $this->actingAs($this->instructorA)->post('/management/courses', [
            'title' => '',
            'category_id' => 9999,
            'thumbnail' => $oversizedFile,
        ]);

        $response->assertSessionHasErrors(['title', 'category_id', 'description', 'delivery_modes', 'thumbnail']);
    }

    public function test_instructor_can_update_their_own_draft_course(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'title' => 'Initial Title',
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true, 'can_manage_content' => true]);

        $response = $this->actingAs($this->instructorA)->put(route('management.courses.update', $course), [
            'category_id' => $this->category->id,
            'title' => 'Updated Title by Instructor',
            'description' => 'Updated syllabus description.',
            'difficulty_level' => 'all_levels',
            'language' => 'English',
            'duration_hours' => 15.0,
            'delivery_modes' => ['online'],
            'online_price' => 6000.00,
        ]);

        $response->assertRedirect(route('management.courses.show', $course));
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Title by Instructor',
        ]);
    }

    public function test_instructor_cannot_update_another_instructors_course(): void
    {
        $courseB = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorB->id,
            'title' => 'Instructor B Course',
            'status' => Course::STATUS_DRAFT,
        ]);
        $courseB->instructors()->attach($this->instructorB->id, ['is_primary' => true]);

        // Instructor A attempts to update Instructor B's course
        $response = $this->actingAs($this->instructorA)->put(route('management.courses.update', $courseB), [
            'category_id' => $this->category->id,
            'title' => 'Hijacked Title',
            'description' => 'Hijacked description.',
            'difficulty_level' => 'all_levels',
            'language' => 'English',
            'duration_hours' => 10.0,
            'delivery_modes' => ['online'],
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('courses', ['title' => 'Hijacked Title']);
    }

    public function test_instructor_can_submit_draft_course_for_review(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        $response = $this->actingAs($this->instructorA)->post(route('management.courses.status', $course), [
            'status' => Course::STATUS_REVIEW,
        ]);

        $response->assertRedirect(route('management.courses.show', $course));
        $this->assertEquals(Course::STATUS_REVIEW, $course->fresh()->status);
    }

    public function test_instructor_cannot_directly_publish_course(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        // Instructor attempts to directly set status to 'published'
        $response = $this->actingAs($this->instructorA)->post(route('management.courses.status', $course), [
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals(Course::STATUS_PUBLISHED, $course->fresh()->status);
    }

    public function test_administrator_can_publish_course_and_set_published_at_timestamp(): void
    {
        $course = Course::factory()->review()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('management.courses.status', $course), [
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('management.courses.show', $course));
        $fresh = $course->fresh();
        $this->assertEquals(Course::STATUS_PUBLISHED, $fresh->status);
        $this->assertNotNull($fresh->published_at);
    }

    public function test_administrator_can_archive_course(): void
    {
        $course = Course::factory()->published()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('management.courses.status', $course), [
            'status' => Course::STATUS_ARCHIVED,
        ]);

        $response->assertRedirect(route('management.courses.show', $course));
        $this->assertEquals(Course::STATUS_ARCHIVED, $course->fresh()->status);
    }

    public function test_instructor_can_delete_their_own_draft_course(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        $response = $this->actingAs($this->instructorA)->delete(route('management.courses.destroy', $course));

        $response->assertRedirect(route('management.courses.index'));
        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_instructor_cannot_delete_published_course(): void
    {
        $course = Course::factory()->published()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        $response = $this->actingAs($this->instructorA)->delete(route('management.courses.destroy', $course));

        $response->assertStatus(403);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'deleted_at' => null]);
    }

    public function test_administrator_can_manage_categories(): void
    {
        $response = $this->actingAs($this->admin)->post(route('management.categories.store'), [
            'name' => 'Data Engineering',
            'slug' => 'data-engineering',
            'icon' => 'fas fa-database',
            'description' => 'Big data pipelines and analytics in Sri Lanka.',
        ]);

        $response->assertRedirect(route('management.categories.index'));
        $this->assertDatabaseHas('categories', ['slug' => 'data-engineering']);
    }

    public function test_instructor_cannot_create_categories(): void
    {
        $response = $this->actingAs($this->instructorA)->post(route('management.categories.store'), [
            'name' => 'Unauthorized Category',
            'slug' => 'unauthorized-category',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', ['slug' => 'unauthorized-category']);
    }

    public function test_authorized_instructor_can_add_curriculum_sections_and_lessons(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true, 'can_manage_content' => true]);

        // Add section
        $sectionResponse = $this->actingAs($this->instructorA)->post(route('management.courses.sections.store', $course), [
            'title' => 'Module 1: Introduction to APIs',
            'description' => 'Basic overview of HTTP and controllers.',
        ]);
        $sectionResponse->assertRedirect(route('management.courses.show', $course));
        $this->assertDatabaseHas('course_sections', [
            'course_id' => $course->id,
            'title' => 'Module 1: Introduction to APIs',
        ]);

        $section = CourseSection::where('course_id', $course->id)->first();

        // Add lesson under section
        $lessonResponse = $this->actingAs($this->instructorA)->post(route('management.sections.lessons.store', $section), [
            'title' => '1.1 Creating Controllers',
            'type' => 'video',
            'duration_minutes' => 20,
            'is_preview' => '1',
        ]);
        $lessonResponse->assertRedirect(route('management.courses.show', $course));
        $this->assertDatabaseHas('lessons', [
            'section_id' => $section->id,
            'title' => '1.1 Creating Controllers',
            'duration_minutes' => 20,
            'is_preview' => true,
        ]);
    }

    public function test_unauthorized_user_cannot_view_unpublished_course_show_page(): void
    {
        $course = Course::factory()->create([
            'category_id' => $this->category->id,
            'created_by_user_id' => $this->instructorA->id,
            'status' => Course::STATUS_DRAFT,
        ]);
        $course->instructors()->attach($this->instructorA->id, ['is_primary' => true]);

        // Instructor B (not assigned) attempts to view
        $responseB = $this->actingAs($this->instructorB)->get(route('management.courses.show', $course));
        $responseB->assertStatus(403);

        // Student attempts to view
        $responseStudent = $this->actingAs($this->student)->get(route('management.courses.show', $course));
        $responseStudent->assertStatus(403);
    }
}
