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
        Schema::table('bundles', function (Blueprint $table) {
            // Rename price to website_price if it exists, otherwise create website_price
            if (Schema::hasColumn('bundles', 'price')) {
                 $table->renameColumn('price', 'website_price');
            } else {
                 $table->decimal('website_price', 10, 2)->default(0)->after('description');
            }

            // Add new pricing fields
            $table->decimal('affiliate_price', 10, 2)->nullable()->after('website_price');
            $table->enum('discount_type', ['flat', 'percentage'])->nullable()->after('affiliate_price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->enum('commission_type', ['flat', 'percentage'])->nullable()->after('discount_value');
            $table->decimal('commission_value', 10, 2)->nullable()->after('commission_type');
            $table->decimal('final_price', 10, 2)->default(0)->after('commission_value');

            // Add indexes
            $table->index('website_price');
            $table->index('is_published'); // is_published serves as status
            $table->index('created_at');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
             if (Schema::hasColumn('bundles', 'website_price')) {
                 $table->renameColumn('website_price', 'price');
            }
            $table->dropColumn([
                'affiliate_price',
                'discount_type',
                'discount_value',
                'commission_type',
                'commission_value'
            ]);
            $table->dropIndex(['website_price']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
