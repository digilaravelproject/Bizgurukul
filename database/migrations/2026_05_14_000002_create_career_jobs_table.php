<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->foreignId('career_job_title_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_job_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_job_experience_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_job_salary_id')->nullable()->constrained()->nullOnDelete();
            $table->longText('description');
            $table->string('apply_link');
            $table->date('posted_on');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('career_job_career_job_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('career_job_skill_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_job_career_job_skill');
        Schema::dropIfExists('career_jobs');
    }
};
