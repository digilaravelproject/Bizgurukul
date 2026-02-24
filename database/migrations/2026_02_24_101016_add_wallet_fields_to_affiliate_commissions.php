<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->decimal('tds_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('payable_amount', 10, 2)->default(0)->after('tds_amount');
            $table->timestamp('available_at')->nullable()->after('payable_amount');
            $table->unsignedBigInteger('withdrawal_request_id')->nullable()->after('available_at');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropColumn(['tds_amount', 'payable_amount', 'available_at', 'withdrawal_request_id']);
        });
    }
};
