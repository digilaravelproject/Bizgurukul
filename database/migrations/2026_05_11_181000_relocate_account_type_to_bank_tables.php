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
        // Remove from users and leads as per user's request to "only keep in KYC/Bank"
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });

        // Add to bank_details and bank_update_requests
        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('account_holder_name');
        });

        Schema::table('bank_update_requests', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('account_holder_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });

        Schema::table('bank_update_requests', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('password');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('mobile');
        });
    }
};
