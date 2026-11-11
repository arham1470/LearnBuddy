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
        Schema::create('batch_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('course_batches')->cascadeOnDelete();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('topic', 255)->nullable();
            $table->string('room_number', 50)->nullable();
            $table->string('status', 30)->default('scheduled'); // scheduled, completed, cancelled
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('conducted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['batch_id', 'session_date']);
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_session_id')->constrained('batch_sessions')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('present'); // present, absent, late, excused
            $table->string('remarks', 255)->nullable();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['batch_session_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('batch_schedule_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('course_batches')->cascadeOnDelete();
            $table->foreignId('changed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('change_type', 50); // rescheduled, cancelled, time_changed, room_changed
            $table->json('previous_details')->nullable();
            $table->json('new_details')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['batch_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_schedule_changes');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('batch_sessions');
    }
};
