<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up orphaned referred_by IDs to prevent foreign key constraint violation
        DB::table('users')
            ->whereNotNull('referred_by')
            ->whereNotIn('referred_by', function($query) {
                $query->select('id')->from('users');
            })
            ->update(['referred_by' => null]);

        // 2. Add the foreign key constraint
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('referred_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
        });
    }
};
