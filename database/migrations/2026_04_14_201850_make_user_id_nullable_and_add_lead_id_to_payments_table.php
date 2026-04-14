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
        Schema::table('payments', function (Blueprint $table) {
            // Make user_id nullable for guest/lead payments
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add lead_id to track payments for unregistered users
            if (!Schema::hasColumn('payments', 'lead_id')) {
                $table->foreignId('lead_id')->nullable()->after('user_id')->constrained('leads')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');
            // Reverting nullable is risky if nulls exist
            // $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
