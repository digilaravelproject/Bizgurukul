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
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique(); // The 'ref' code, e.g., 'u/Prrojects' or 'u/xyz123'
            $table->string('target_type')->default('all'); // bundle, course, all
            $table->string('target_id')->nullable(); // ID of the course/bundle if specific
            $table->timestamp('expires_at')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'target_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
