<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title', 255);
            $table->string('slug', 280)->unique();
            $table->string('subtitle', 500)->nullable();
            $table->longText('description');
            $table->string('thumbnail_url', 1024)->nullable();
            $table->string('preview_video_url', 1024)->nullable();
            $table->string('difficulty_level', 30)->default('all_levels');
            $table->string('language', 50)->default('English');
            $table->decimal('duration_hours', 6, 2)->default(0.00);
            $table->json('learning_outcomes')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('status', 30)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index('difficulty_level');
        });

        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_manage_content')->default(true);
            $table->boolean('can_grade')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_instructors');
        Schema::dropIfExists('courses');
    }
};
