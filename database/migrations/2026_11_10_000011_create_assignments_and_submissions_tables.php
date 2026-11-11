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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 280);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->unsignedInteger('max_points')->default(100);
            $table->unsignedInteger('passing_points')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('allowed_file_types', 255)->default('pdf,zip,doc,docx,txt,png,jpg,jpeg');
            $table->unsignedInteger('max_file_size_mb')->default(10);
            $table->boolean('is_published')->default(true);
            $table->boolean('allow_late_submissions')->default(false);
            $table->timestamps();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'is_published']);
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('file_original_name', 255);
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('file_mime_type', 100);
            $table->text('student_notes')->nullable();
            $table->dateTime('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->string('status', 30)->default('submitted'); // submitted, graded, returned
            $table->unsignedInteger('points_awarded')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
