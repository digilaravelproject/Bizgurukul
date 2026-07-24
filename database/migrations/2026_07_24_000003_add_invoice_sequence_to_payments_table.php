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
        if (!Schema::hasColumn('payments', 'invoice_sequence')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedInteger('invoice_sequence')->nullable()->after('status')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'invoice_sequence')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('invoice_sequence');
            });
        }
    }
};
