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
        Schema::create('physical_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('city', 100);
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('google_map_embed_url')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->unsignedInteger('max_room_capacity')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_delivery_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('delivery_mode', 30); // online, physical, hybrid
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->string('currency', 3)->default('LKR');
            $table->boolean('is_free')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_capacity')->nullable();
            $table->unsignedInteger('access_period_days')->nullable();
            $table->boolean('includes_recorded_lessons')->default(true);
            $table->boolean('includes_physical_sessions')->default(false);
            $table->boolean('includes_live_classes')->default(false);
            $table->boolean('includes_certificate')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'delivery_mode', 'title']);
        });

        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_delivery_option_id')->constrained('course_delivery_options')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('physical_locations')->restrictOnDelete();
            $table->string('batch_code', 50)->unique();
            $table->string('batch_name', 150);
            $table->unsignedInteger('max_seats');
            $table->unsignedInteger('enrolled_seats')->default(0);
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 30)->default('upcoming');
            $table->timestamps();
        });

        Schema::create('batch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('course_batches')->cascadeOnDelete();
            $table->string('day_of_week', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_schedules');
        Schema::dropIfExists('course_batches');
        Schema::dropIfExists('course_delivery_options');
        Schema::dropIfExists('physical_locations');
    }
};
