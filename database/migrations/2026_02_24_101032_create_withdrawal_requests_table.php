<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('tds_deducted', 10, 2)->default(0);
            $table->decimal('payable_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, processing, approved, rejected
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->foreign('withdrawal_request_id')->references('id')->on('withdrawal_requests')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_request_id']);
        });

        Schema::dropIfExists('withdrawal_requests');
    }
};
