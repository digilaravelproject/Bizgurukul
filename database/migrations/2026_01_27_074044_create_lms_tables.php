<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Lessons Table (Video paths ke liye)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('video_path'); // Original file (MP4)
            $table->string('hls_path')->nullable(); // Encrypted/Processed path (m3u8)
            $table->integer('order_column')->default(0); // Sequence ke liye
            $table->timestamps();
        });

        // 2. Video Progress Table (Resume Playback & Heartbeat)
        Schema::create('video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->integer('last_watched_second')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 3. Certificates Table
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('certificate_no')->unique();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_tables');
    }
};
