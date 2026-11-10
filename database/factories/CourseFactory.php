<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'category_id' => Category::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'subtitle' => fake()->sentence(8),
            'description' => fake()->paragraphs(3, true),
            'thumbnail_url' => null,
            'preview_video_url' => null,
            'difficulty_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'all_levels']),
            'language' => 'English',
            'duration_hours' => fake()->randomFloat(2, 5, 60),
            'learning_outcomes' => [
                'Understand core architectural patterns',
                'Write scalable clean code',
                'Implement secure database operations',
            ],
            'prerequisites' => [
                'Basic computer literacy',
            ],
            'status' => Course::STATUS_DRAFT,
            'published_at' => null,
            'created_by_user_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
    }

    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_REVIEW,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_ARCHIVED,
        ]);
    }
}
