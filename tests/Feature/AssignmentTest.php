<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected User $otherInstructor;

    protected User $student;

    protected User $otherStudent;

    protected Course $course;

    protected CourseDeliveryOption $deliveryOption;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->seed(RoleSeeder::class);

        $category = Category::create([
            'name' => 'Software Engineering',
            'slug' => 'software-engineering',
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
            'title' => 'Full-Stack Web Development',
            'slug' => 'full-stack-web-development',
            'description' => 'A comprehensive course on full-stack development.',
            'difficulty_level' => 'intermediate',
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

        $this->deliveryOption = CourseDeliveryOption::create([
            'course_id' => $this->course->id,
            'title' => 'Online Access',
            'delivery_mode' => 'online',
            'price' => 0.00,
            'currency' => 'LKR',
            'is_active' => true,
        ]);

        // Enroll primary student
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-ASSIGN-01',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 0.00,
            'is_free' => true,
            'enrolled_at' => now(),
        ]);
    }

    public function test_authorized_instructor_can_create_assignment(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('management.courses.assignments.store', $this->course), [
                'title' => 'Final Capstone Project',
                'description' => 'Build a responsive SaaS application.',
                'instructions' => 'Include database migrations, models, and tests.',
                'max_points' => 100,
                'passing_points' => 50,
                'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'allowed_file_types' => 'pdf,zip',
                'max_file_size_mb' => 20,
                'is_published' => true,
                'allow_late_submissions' => false,
            ]);

        $response->assertRedirect(route('management.courses.assignments.index', $this->course));
        $this->assertDatabaseHas('assignments', [
            'course_id' => $this->course->id,
            'title' => 'Final Capstone Project',
            'max_points' => 100,
            'allowed_file_types' => 'pdf,zip',
        ]);
    }

    public function test_unauthorized_instructor_cannot_create_assignment_for_course(): void
    {
        $response = $this->actingAs($this->otherInstructor)
            ->post(route('management.courses.assignments.store', $this->course), [
                'title' => 'Unauthorized Assignment',
                'max_points' => 100,
                'max_file_size_mb' => 10,
            ]);

        $response->assertStatus(403);
    }

    public function test_enrolled_student_can_view_published_assignment(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Homework 1',
            'max_points' => 50,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('courses.assignments.show', [$this->course, $assignment]));

        $response->assertStatus(200);
        $response->assertSee('Homework 1');
    }

    public function test_non_enrolled_student_cannot_view_or_submit_assignment(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Homework 1',
            'max_points' => 50,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        // Non-enrolled student cannot view
        $viewResponse = $this->actingAs($this->otherStudent)
            ->get(route('courses.assignments.show', [$this->course, $assignment]));

        $viewResponse->assertStatus(403);

        // Non-enrolled student cannot submit
        $file = UploadedFile::fake()->create('homework.pdf', 500, 'application/pdf');

        $submitResponse = $this->actingAs($this->otherStudent)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
            ]);

        $submitResponse->assertStatus(403);
    }

    public function test_enrolled_student_can_submit_valid_file(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Project Alpha',
            'max_points' => 100,
            'allowed_file_types' => 'pdf,zip',
            'max_file_size_mb' => 10,
            'is_published' => true,
        ]);

        $file = UploadedFile::fake()->create('project.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->student)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
                'student_notes' => 'Here is my submission draft.',
            ]);

        $response->assertRedirect(route('courses.assignments.show', [$this->course, $assignment]));
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'status' => 'submitted',
            'is_late' => false,
            'student_notes' => 'Here is my submission draft.',
        ]);
    }

    public function test_disallowed_file_type_is_rejected(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Essay Assignment',
            'max_points' => 100,
            'allowed_file_types' => 'pdf,docx',
            'max_file_size_mb' => 10,
            'is_published' => true,
        ]);

        // Uploading an executable / script
        $file = UploadedFile::fake()->create('script.exe', 500, 'application/x-msdownload');

        $response = $this->actingAs($this->student)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
        ]);
    }

    public function test_file_exceeding_max_size_is_rejected(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Small File Assignment',
            'max_points' => 100,
            'allowed_file_types' => 'pdf',
            'max_file_size_mb' => 2, // 2MB max
            'is_published' => true,
        ]);

        // 5MB file exceeds 2MB limit
        $file = UploadedFile::fake()->create('large_doc.pdf', 5 * 1024, 'application/pdf');

        $response = $this->actingAs($this->student)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_past_due_submission_is_rejected_when_late_submissions_disabled(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Strict Deadline Assignment',
            'max_points' => 100,
            'due_date' => now()->subDay(), // Expired
            'allow_late_submissions' => false,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        $file = UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->student)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_past_due_submission_is_accepted_and_marked_late_when_late_submissions_enabled(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Flexible Deadline Assignment',
            'max_points' => 100,
            'due_date' => now()->subHour(),
            'allow_late_submissions' => true,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        $file = UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->student)
            ->post(route('courses.assignments.submit', [$this->course, $assignment]), [
                'file' => $file,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'is_late' => true,
        ]);
    }

    public function test_student_cannot_access_or_download_another_students_submission_idor(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Security Assignment',
            'max_points' => 100,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        // Fake a stored file
        $path = 'assignments/submissions/'.$assignment->id.'/testfile.pdf';
        Storage::disk('local')->put($path, 'Student 1 Private Content');

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'file_path' => $path,
            'file_original_name' => 'testfile.pdf',
            'file_size_bytes' => 100,
            'file_mime_type' => 'application/pdf',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // otherStudent tries to download student's submission -> 403 Forbidden
        $response = $this->actingAs($this->otherStudent)
            ->get(route('assignments.submissions.download', $submission));

        $response->assertStatus(403);
    }

    public function test_submitting_student_can_download_their_own_submission(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Downloadable Assignment',
            'max_points' => 100,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        $path = 'assignments/submissions/'.$assignment->id.'/testfile.pdf';
        Storage::disk('local')->put($path, 'Student 1 Private Content');

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'file_path' => $path,
            'file_original_name' => 'testfile.pdf',
            'file_size_bytes' => 100,
            'file_mime_type' => 'application/pdf',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('assignments.submissions.download', $submission));

        $response->assertStatus(200);
    }

    public function test_course_instructor_can_download_and_grade_student_submission(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Grading Assignment',
            'max_points' => 100,
            'is_published' => true,
            'max_file_size_mb' => 10,
        ]);

        $path = 'assignments/submissions/'.$assignment->id.'/testfile.pdf';
        Storage::disk('local')->put($path, 'Student Submission Content');

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->student->id,
            'file_path' => $path,
            'file_original_name' => 'testfile.pdf',
            'file_size_bytes' => 100,
            'file_mime_type' => 'application/pdf',
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Instructor downloads submission
        $downloadResponse = $this->actingAs($this->instructor)
            ->get(route('assignments.submissions.download', $submission));

        $downloadResponse->assertStatus(200);

        // Instructor grades submission
        $gradeResponse = $this->actingAs($this->instructor)
            ->post(route('management.courses.assignments.grade', [$this->course, $assignment, $submission]), [
                'points_awarded' => 95,
                'feedback' => 'Excellent work on the project!',
            ]);

        $gradeResponse->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'points_awarded' => 95,
            'status' => 'graded',
            'graded_by_user_id' => $this->instructor->id,
            'feedback' => 'Excellent work on the project!',
        ]);
    }
}
