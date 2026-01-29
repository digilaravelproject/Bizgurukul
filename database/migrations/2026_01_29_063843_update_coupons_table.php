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
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'coupon_type')) {
                $table->enum('coupon_type', ['general', 'specific'])->default('general');
            }

            if (!Schema::hasColumn('coupons', 'selected_courses')) {
                $table->json('selected_courses')->nullable();
            }

            if (!Schema::hasColumn('coupons', 'selected_bundles')) {
                $table->json('selected_bundles')->nullable();
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
};
