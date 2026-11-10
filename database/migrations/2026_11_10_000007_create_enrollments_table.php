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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_delivery_option_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('enrollment_code', 32)->unique();
            $table->string('delivery_mode', 20)->default('online'); // online, physical, hybrid
            $table->string('status', 30)->default('active'); // active, pending_payment, cancelled, completed, expired
            $table->decimal('price_paid', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('LKR');
            $table->boolean('is_free')->default(false);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate active or pending enrollments for the same user and course
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['course_batch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
