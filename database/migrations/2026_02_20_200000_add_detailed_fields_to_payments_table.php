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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->after('amount')->nullable();
            $table->decimal('discount_amount', 10, 2)->after('subtotal')->default(0);
            $table->decimal('tax_amount', 10, 2)->after('discount_amount')->default(0);
            $table->decimal('total_amount', 10, 2)->after('tax_amount')->nullable();
            $table->foreignId('coupon_id')->nullable()->after('total_amount')->constrained('coupons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['subtotal', 'discount_amount', 'tax_amount', 'total_amount', 'coupon_id']);
        });
    }
};
