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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('course_batches')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('scheduled_start_time');
            $table->dateTime('scheduled_end_time');
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->string('provider', 50)->default('jitsi');
            $table->string('meeting_identifier', 255)->unique();
            $table->string('meeting_password', 255)->nullable();
            $table->string('status', 30)->default('scheduled'); // scheduled, live, completed, cancelled
            $table->string('access_permission', 30)->default('enrolled_students'); // enrolled_students, batch_students_only, instructors_only
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes for fast lookup
            $table->index(['course_id', 'scheduled_start_time']);
            $table->index(['instructor_id', 'status']);
            $table->index(['status', 'scheduled_start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
