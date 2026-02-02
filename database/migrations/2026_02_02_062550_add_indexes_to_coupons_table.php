<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->index(['code', 'is_active', 'expiry_date'], 'idx_coupon_search_optimization');
            $table->index('coupon_type');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('idx_coupon_search_optimization');
            $table->dropIndex(['coupon_type']);
        });
    }
};
