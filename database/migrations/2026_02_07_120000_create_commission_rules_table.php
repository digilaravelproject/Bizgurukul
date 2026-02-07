<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();

            // If null, applies to all affiliates (Global Rule)
            // If set, applies to specific affiliate
            $table->foreignId('affiliate_id')->nullable()->constrained('users')->nullOnDelete();

            // If null, applies globally (to all products)
            // If set, applies to specific product (Course/Bundle)
            $table->nullableMorphs('product');

            $table->string('commission_type')->default('percent'); // 'percent' or 'fixed'
            $table->decimal('amount', 10, 2);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
