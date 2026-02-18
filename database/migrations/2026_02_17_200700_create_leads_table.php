<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index(); // Indexed for fast lookup
            $table->string('mobile')->nullable();
            $table->string('password')->nullable(); // Hashed password, can be null initially
            $table->string('referral_code')->nullable(); // Sponsor code
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('state')->nullable();
            $table->json('product_preference')->nullable(); // Store product selection context (bundle_id, etc.)
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
