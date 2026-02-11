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
        Schema::table('coupon_packages', function (Blueprint $table) {
            // Jo columns missing the, wo add karein
            if (!Schema::hasColumn('coupon_packages', 'type')) {
                $table->string('type')->default('fixed')->after('description');
            }
            if (!Schema::hasColumn('coupon_packages', 'used_count')) {
                $table->integer('used_count')->default(0)->after('discount_price');
            }
            if (!Schema::hasColumn('coupon_packages', 'couponable_type')) {
                $table->string('couponable_type')->nullable()->after('used_count');
                $table->unsignedBigInteger('couponable_id')->nullable()->after('couponable_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
