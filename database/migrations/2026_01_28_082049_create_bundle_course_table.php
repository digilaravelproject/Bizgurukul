<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_course', function (Blueprint $table) {
            $table->id();
            // Relationship with Bundle
            $table->foreignId('bundle_id')->constrained()->onDelete('cascade');
            // Relationship with Course
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            // Bhai, ye unique constraint hi duplicate courses ko rokega
            $table->unique(['bundle_id', 'course_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_course');
    }
};
