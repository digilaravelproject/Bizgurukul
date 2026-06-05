<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_job_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action_type'); // 'view' or 'apply'
            $table->timestamps();

            // Unique constraint to prevent duplicate views/applies per user per job
            $table->unique(['career_job_id', 'user_id', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_job_clicks');
    }
};
