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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Example: PRO2026
            $table->enum('type', ['fixed', 'percentage']); // Discount type
            $table->decimal('value', 10, 2); // Discount amount or percentage
            $table->date('expiry_date')->nullable();
            $table->integer('usage_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);

            // Polymorphic Fields: Isse ek hi table se Course aur Bundle dono link honge
            $table->nullableMorphs('couponable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
