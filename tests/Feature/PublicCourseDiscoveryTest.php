<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseDeliveryOption;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCourseDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;

    protected User $student;

    protected Category $softwareCategory;

    protected Category $webCategory;

    protected Course $publishedCourse;

    protected Course $draftCourse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->softwareCategory = Category::firstOrCreate(
            ['slug' => 'software-engineering'],
            [
                'name' => 'Software Engineering',
                'description' => 'Software systems and enterprise architecture',
                'sort_order' => 1,
            ]
        );

        $this->webCategory = Category::firstOrCreate(
            ['slug' => 'web-development'],
            [
                'name' => 'Web Development',
                'description' => 'Modern web interfaces and CSS',
                'sort_order' => 2,
            ]
        );

        $this->instructor = User::firstOrCreate(
            ['email' => 'instructor@learnbuddy.lk'],
            [
                'name' => 'Dilshan Perera (Instructor)',
                'password' => bcrypt('InstructorPass123!'),
                'is_active' => true,
            ]
        );
        $this->instructor->assignRole('instructor');

        $this->student = User::firstOrCreate(
            ['email' => 'student@learnbuddy.lk'],
            [
                'name' => 'Chamara Silva (Student)',
                'password' => bcrypt('StudentPass123!'),
                'is_active' => true,
            ]
        );
        $this->student->assignRole('student');

        // Create standard test published course
        $this->publishedCourse = Course::firstOrCreate(
            ['slug' => 'test-fullstack-laravel-discovery'],
            [
                'category_id' => $this->softwareCategory->id,
                'title' => 'Test Full-Stack Laravel for Discovery',
                'subtitle' => 'Modern PHP web development in Sri Lanka',
                'description' => 'Comprehensive masterclass on Laravel and Tailwind architecture.',
                'difficulty_level' => 'intermediate',
                'language' => 'English',
                'duration_hours' => 25.0,
                'learning_outcomes' => ['Build web apps', 'Secure databases'],
                'status' => Course::STATUS_PUBLISHED,
                'published_at' => now()->subDay(),
                'created_by_user_id' => $this->instructor->id,
            ]
        );

        $this->publishedCourse->instructors()->syncWithoutDetaching([
            $this->instructor->id => [
                'is_primary' => true,
                'can_manage_content' => true,
                'can_grade' => true,
            ],
        ]);

        CourseDeliveryOption::firstOrCreate(
            ['course_id' => $this->publishedCourse->id, 'delivery_mode' => 'online', 'title' => 'Online Access'],
            [
                'price' => 7500.00,
                'currency' => 'LKR',
                'is_free' => false,
                'is_active' => true,
            ]
        );

        // Create standard test draft course
        $this->draftCourse = Course::firstOrCreate(
            ['slug' => 'test-draft-private-course'],
            [
                'category_id' => $this->softwareCategory->id,
                'title' => 'Top Secret Unreleased Course',
                'subtitle' => 'Should never be seen publicly',
                'description' => 'Draft unpublished syllabus content.',
                'status' => Course::STATUS_DRAFT,
                'published_at' => null,
                'created_by_user_id' => $this->instructor->id,
            ]
        );
    }

    public function test_homepage_displays_published_courses_and_categories(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);
        $response->assertDontSee($this->draftCourse->title);
        $response->assertSee($this->softwareCategory->name);
    }

    public function test_catalogue_lists_published_courses_and_hides_drafts(): void
    {
        $response = $this->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);
        $response->assertDontSee($this->draftCourse->title);
    }

    public function test_unpublished_course_detail_route_returns_404(): void
    {
        $response = $this->get(route('courses.show', $this->draftCourse));

        $response->assertStatus(404);
    }

    public function test_published_course_detail_page_is_accessible(): void
    {
        $response = $this->get(route('courses.show', $this->publishedCourse));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);
        $response->assertSee('Build web apps');
        $response->assertSee('LKR 7,500.00');
        $response->assertSee($this->instructor->name);
    }

    public function test_keyword_search_filters_courses(): void
    {
        $response = $this->get(route('courses.index', ['q' => 'Test Full-Stack']));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);

        $emptyResponse = $this->get(route('courses.index', ['q' => 'NonExistentSearchTerm123456']));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('No matching published courses');
    }

    public function test_category_filter_narrows_catalogue(): void
    {
        $response = $this->get(route('courses.index', ['category' => 'software-engineering']));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);
    }

    public function test_delivery_mode_filter_filters_results(): void
    {
        $response = $this->get(route('courses.index', ['delivery_mode' => 'online']));

        $response->assertStatus(200);
        $response->assertSee($this->publishedCourse->title);
    }

    public function test_price_filter_filters_free_and_paid(): void
    {
        // Paid search
        $responsePaid = $this->get(route('courses.index', ['price' => 'paid']));
        $responsePaid->assertStatus(200);
        $responsePaid->assertSee($this->publishedCourse->title);

        // Free search
        $responseFree = $this->get(route('courses.index', ['price' => 'free']));
        $responseFree->assertStatus(200);
    }

    public function test_instructor_profile_page_displays_only_published_courses(): void
    {
        $response = $this->get(route('instructors.show', $this->instructor));

        $response->assertStatus(200);
        $response->assertSee($this->instructor->name);
        $response->assertSee($this->publishedCourse->title);
        $response->assertDontSee($this->draftCourse->title);
    }

    public function test_instructor_profile_returns_404_for_student_without_courses(): void
    {
        $response = $this->get(route('instructors.show', $this->student));

        $response->assertStatus(404);
    }

    public function test_real_review_data_is_displayed_honestly_without_invented_data(): void
    {
        // Add a real review to published course
        Review::firstOrCreate(
            ['course_id' => $this->publishedCourse->id, 'user_id' => $this->student->id],
            [
                'rating' => 5,
                'title' => 'Top notch training in Colombo',
                'comment' => 'Clear practical instructions on Laravel architecture.',
                'is_published' => true,
            ]
        );

        $response = $this->get(route('courses.show', $this->publishedCourse));

        $response->assertStatus(200);
        $response->assertSee('Top notch training in Colombo');
        $response->assertSee('Clear practical instructions on Laravel architecture.');
        $response->assertSee('5.0');
    }
}
