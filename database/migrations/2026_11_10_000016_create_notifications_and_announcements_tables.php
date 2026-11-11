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
        // 1. Standard Laravel Database Notifications Table
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
            });
        }

        // 2. User Notification Preferences Table
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('email_enrollment')->default(true);
            $table->boolean('email_payment')->default(true);
            $table->boolean('email_live_class')->default(true);
            $table->boolean('email_assignments')->default(true);
            $table->boolean('email_quizzes')->default(true);
            $table->boolean('email_announcements')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->boolean('push_notifications')->default(false);
            $table->timestamps();
        });

        // 3. Course Announcements Table
        Schema::create('course_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();

            $table->index(['course_id', 'published_at']);
        });

        // 4. Notification Delivery Logs & Deduplication Table
        Schema::create('notification_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('deduplication_key', 150)->unique();
            $table->string('channel', 30)->default('database');
            $table->string('status', 30)->default('sent'); // queued, sent, failed, skipped
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'event_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_delivery_logs');
        Schema::dropIfExists('course_announcements');
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('notifications');
    }
};
