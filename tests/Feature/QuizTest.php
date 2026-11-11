<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
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

        $this->seed(RoleSeeder::class);

        $category = Category::create([
            'name' => 'Data Science',
            'slug' => 'data-science',
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
            'title' => 'Python for Machine Learning',
            'slug' => 'python-for-machine-learning',
            'description' => 'A comprehensive course on python and machine learning.',
            'difficulty_level' => 'intermediate',
            'language' => 'English',
            'duration_hours' => 15,
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

        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'course_delivery_option_id' => $this->deliveryOption->id,
            'enrollment_code' => 'ENR-QUIZ-01',
            'delivery_mode' => 'online',
            'status' => Enrollment::STATUS_ACTIVE,
            'price_paid' => 0.00,
            'is_free' => true,
            'enrolled_at' => now(),
        ]);
    }

    public function test_authorized_instructor_can_create_quiz_and_questions(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('management.courses.quizzes.store', $this->course), [
                'title' => 'Module 1 Assessment',
                'description' => 'Test your understanding of NumPy and Pandas.',
                'instructions' => 'Read each question carefully before answering.',
                'time_limit_minutes' => 30,
                'passing_percentage' => 70,
                'max_attempts' => 2,
                'is_published' => true,
                'show_correct_answers_after_attempt' => true,
            ]);

        $this->assertDatabaseHas('quizzes', [
            'course_id' => $this->course->id,
            'title' => 'Module 1 Assessment',
            'passing_percentage' => 70,
            'max_attempts' => 2,
        ]);

        $quiz = Quiz::where('course_id', $this->course->id)->first();

        // Add a question to this quiz
        $qResponse = $this->actingAs($this->instructor)
            ->post(route('management.courses.quizzes.questions.store', [$this->course, $quiz]), [
                'question_text' => 'What is the primary data structure of Pandas?',
                'question_type' => 'single_choice',
                'marks' => 2,
                'explanation' => 'DataFrame is a 2D labeled data structure with columns of potentially different types.',
                'options' => [
                    ['text' => 'DataFrame'],
                    ['text' => 'Tensor'],
                    ['text' => 'LinkedList'],
                    ['text' => 'Queue'],
                ],
                'correct_option_index' => 0, // DataFrame is correct
            ]);

        $qResponse->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_text' => 'What is the primary data structure of Pandas?',
            'marks' => 2,
        ]);

        $this->assertDatabaseHas('quiz_question_options', [
            'option_text' => 'DataFrame',
            'is_correct' => true,
        ]);

        $this->assertDatabaseHas('quiz_question_options', [
            'option_text' => 'Tensor',
            'is_correct' => false,
        ]);
    }

    public function test_unauthorized_instructor_cannot_create_quiz(): void
    {
        $response = $this->actingAs($this->otherInstructor)
            ->post(route('management.courses.quizzes.store', $this->course), [
                'title' => 'Sneaky Quiz',
                'passing_percentage' => 50,
                'max_attempts' => 1,
            ]);

        $response->assertStatus(403);
    }

    public function test_enrolled_student_can_view_published_quiz(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Basics Quiz',
            'passing_percentage' => 60,
            'max_attempts' => 3,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('courses.quizzes.show', [$this->course, $quiz]));

        $response->assertStatus(200);
        $response->assertSee('Basics Quiz');
    }

    public function test_non_enrolled_student_cannot_view_or_attempt_quiz(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Basics Quiz',
            'passing_percentage' => 60,
            'max_attempts' => 3,
            'is_published' => true,
        ]);

        // Attempting to view -> 403
        $viewResponse = $this->actingAs($this->otherStudent)
            ->get(route('courses.quizzes.show', [$this->course, $quiz]));

        $viewResponse->assertStatus(403);

        // Attempting to start -> 403
        $startResponse = $this->actingAs($this->otherStudent)
            ->post(route('courses.quizzes.start', [$this->course, $quiz]));

        $startResponse->assertStatus(403);
    }

    public function test_quiz_attempt_questions_do_not_expose_correct_answers_during_take(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Secure Quiz',
            'passing_percentage' => 60,
            'max_attempts' => 1,
            'is_published' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What is 2 + 2?',
            'question_type' => 'single_choice',
            'marks' => 1,
        ]);

        $opt1 = QuizQuestionOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => '4',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $opt2 = QuizQuestionOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => '5',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        // Student starts quiz
        $startResponse = $this->actingAs($this->student)
            ->post(route('courses.quizzes.start', [$this->course, $quiz]));

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $this->student->id)->first();
        $this->assertNotNull($attempt);

        // Student loads the take view
        $takeResponse = $this->actingAs($this->student)
            ->get(route('courses.quizzes.take', [$this->course, $quiz, $attempt]));

        $takeResponse->assertStatus(200);

        // Confirm that the view does NOT output any indicator of which option is correct
        $content = $takeResponse->getContent();
        $this->assertStringNotContainsString('is_correct', $content);
        $this->assertStringNotContainsString('correct', $content);
    }

    public function test_student_attempt_scoring_is_calculated_securely_server_side(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Math & Logic Quiz',
            'passing_percentage' => 50,
            'max_attempts' => 1,
            'is_published' => true,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What is 5 * 5?',
            'question_type' => 'single_choice',
            'marks' => 10,
        ]);
        $q1Correct = QuizQuestionOption::create([
            'quiz_question_id' => $q1->id,
            'option_text' => '25',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        $q1Wrong = QuizQuestionOption::create([
            'quiz_question_id' => $q1->id,
            'option_text' => '20',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $q2 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Is Earth flat?',
            'question_type' => 'true_false',
            'marks' => 10,
        ]);
        $q2False = QuizQuestionOption::create([
            'quiz_question_id' => $q2->id,
            'option_text' => 'False',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        $q2True = QuizQuestionOption::create([
            'quiz_question_id' => $q2->id,
            'option_text' => 'True',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        // Student starts attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $this->student->id,
            'attempt_number' => 1,
            'total_marks' => 20,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Student answers Q1 correctly (25) and Q2 incorrectly (True)
        $submitResponse = $this->actingAs($this->student)
            ->post(route('courses.quizzes.submit', [$this->course, $quiz, $attempt]), [
                'answers' => [
                    $q1->id => $q1Correct->id,
                    $q2->id => $q2True->id,
                ],
            ]);

        $submitResponse->assertRedirect(route('courses.quizzes.result', [$this->course, $quiz, $attempt]));

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertEquals(10.00, (float) $attempt->score);
        $this->assertEquals(20, $attempt->total_marks);
        $this->assertEquals(50.00, (float) $attempt->percentage);
        $this->assertTrue($attempt->is_passed); // 50% >= 50% passing threshold

        // Verify individual answers recorded
        $this->assertDatabaseHas('quiz_attempt_answers', [
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $q1->id,
            'selected_option_id' => $q1Correct->id,
            'is_correct' => true,
            'marks_awarded' => 10,
        ]);

        $this->assertDatabaseHas('quiz_attempt_answers', [
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $q2->id,
            'selected_option_id' => $q2True->id,
            'is_correct' => false,
            'marks_awarded' => 0,
        ]);
    }

    public function test_max_attempt_limit_is_enforced(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Single Attempt Quiz',
            'passing_percentage' => 70,
            'max_attempts' => 1, // Only 1 attempt allowed
            'is_published' => true,
        ]);

        // First attempt already completed
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $this->student->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'score' => 50,
            'total_marks' => 100,
            'percentage' => 50,
            'is_passed' => false,
            'started_at' => now()->subHour(),
            'completed_at' => now()->subMinutes(30),
        ]);

        // Student tries to start another attempt -> 403 Forbidden
        $response = $this->actingAs($this->student)
            ->post(route('courses.quizzes.start', [$this->course, $quiz]));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_another_students_attempt_idor(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Privacy Quiz',
            'passing_percentage' => 60,
            'max_attempts' => 2,
            'is_published' => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $this->student->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'score' => 80,
            'total_marks' => 100,
            'percentage' => 80,
            'is_passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // otherStudent tries to view student's attempt results -> 403 Forbidden
        $response = $this->actingAs($this->otherStudent)
            ->get(route('courses.quizzes.result', [$this->course, $quiz, $attempt]));

        $response->assertStatus(403);
    }

    public function test_correct_answers_are_hidden_on_result_when_configured(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Strict Exam',
            'passing_percentage' => 70,
            'max_attempts' => 1,
            'is_published' => true,
            'show_correct_answers_after_attempt' => false, // Hidden
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $this->student->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'score' => 50,
            'total_marks' => 100,
            'percentage' => 50,
            'is_passed' => false,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('courses.quizzes.result', [$this->course, $quiz, $attempt]));

        $response->assertStatus(200);
        $response->assertSee('Answer explanations and solutions are not disclosed for this assessment.');
    }

    public function test_repeated_submit_request_on_completed_attempt_is_handled_safely(): void
    {
        $quiz = Quiz::create([
            'course_id' => $this->course->id,
            'created_by_user_id' => $this->instructor->id,
            'title' => 'Idempotent Quiz',
            'passing_percentage' => 70,
            'max_attempts' => 1,
            'is_published' => true,
        ]);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $this->student->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'score' => 90,
            'total_marks' => 100,
            'percentage' => 90,
            'is_passed' => true,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
        ]);

        // Attempting to re-submit on an already completed attempt safely redirects to results without changing score
        $response = $this->actingAs($this->student)
            ->post(route('courses.quizzes.submit', [$this->course, $quiz, $attempt]), [
                'answers' => [],
            ]);

        $response->assertRedirect(route('courses.quizzes.result', [$this->course, $quiz, $attempt]));
        $attempt->refresh();
        $this->assertEquals(90, $attempt->score);
    }
}
