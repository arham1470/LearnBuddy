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
        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('file_type', 50)->default('pdf'); // pdf, zip, code, link
            $table->string('external_url', 500)->nullable();
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();

            $table->index('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_resources');
    }
};
