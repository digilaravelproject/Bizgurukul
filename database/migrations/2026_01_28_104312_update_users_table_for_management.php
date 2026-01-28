<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. KYC Status
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->enum('kyc_status', ['pending', 'verified', 'rejected', 'not_submitted'])
                    ->default('not_submitted')
                    ->after('is_active')
                    ->index();
            }

            // 2. Ban System
            if (!Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(0)->after('kyc_status');
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            }

            // 3. Soft Deletes (Trash System)
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            // 4. Performance Indexes
            // Check if index exists before adding to avoid errors
            try {
                $table->index(['is_active', 'is_banned', 'deleted_at'], 'users_status_index');
                $table->index(['name', 'email', 'mobile'], 'users_search_index');
            } catch (\Exception $e) {
                // Indexes might already exist
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_status', 'is_banned', 'banned_at', 'deleted_at']);
            // Drop indexes if needed
            $table->dropIndex('users_status_index');
            $table->dropIndex('users_search_index');
        });
    }
};
