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
        Schema::table('bundles', function (Blueprint $table) {
            if (!Schema::hasColumn('bundles', 'preference_index')) {
                $table->integer('preference_index')->default(0)->index()->after('description'); // Adjust 'after' as needed
            }
            if (!Schema::hasColumn('bundles', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->default(0)->after('final_price');
                // Model has 'website_price', 'affiliate_price', 'final_price'. User said 'price'. I'll put it after 'final_price' or 'affiliate_price'.
                // I'll put it after 'affiliate_price'.
            }
            if (!Schema::hasColumn('bundles', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_published');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['preference_index', 'commission_amount', 'is_active']);
        });
    }
};
