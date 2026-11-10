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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('video_upload_id', 255)->nullable()->after('video_playback_id');
            $table->string('video_status', 50)->default('none')->after('video_upload_id'); // none, waiting_upload, processing, ready, errored
            $table->text('video_error_message')->nullable()->after('video_status');
            $table->string('video_playback_policy', 50)->default('signed')->after('video_error_message');
            $table->string('video_aspect_ratio', 20)->nullable()->after('video_playback_policy');

            $table->index('video_upload_id');
            $table->index('video_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['video_upload_id']);
            $table->dropIndex(['video_status']);

            $table->dropColumn([
                'video_upload_id',
                'video_status',
                'video_error_message',
                'video_playback_policy',
                'video_aspect_ratio',
            ]);
        });
    }
};
