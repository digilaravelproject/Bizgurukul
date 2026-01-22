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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 1. Basic Info
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); // LMS Field
            $table->date('dob')->nullable(); // LMS Field

            // 2. Contact Info
            $table->string('email')->unique();
            $table->string('mobile')->nullable(); // LMS Field
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // 3. Profile & Address
            $table->string('profile_picture')->nullable(); // LMS Field
            $table->text('address')->nullable(); // LMS Field
            $table->string('city')->nullable(); // LMS Field
            $table->string('zip_code')->nullable(); // LMS Field

            // 4. Foreign Key (State)
            // Note: Ensure 'states' table migration runs BEFORE this file
            $table->unsignedBigInteger('state_id')->nullable(); // Sirf column banayein, foreign key nahi

            // 5. Account Status
            $table->boolean('is_active')->default(1)->comment('1=Active, 0=Blocked');

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
