<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop base_price if exists
        if (Schema::hasColumn('courses', 'base_price')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('base_price');
            });
        }

        Schema::table('courses', function (Blueprint $table) {

            // Pricing
            if (!Schema::hasColumn('courses', 'website_price')) {
                $table->decimal('website_price', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('courses', 'affiliate_price')) {
                $table->decimal('affiliate_price', 10, 2)->nullable();
            }

            // Discount
            if (!Schema::hasColumn('courses', 'discount_type')) {
                $table->enum('discount_type', ['fixed', 'percent'])
                      ->default('fixed');
            }

            if (!Schema::hasColumn('courses', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)
                      ->default(0);
            }

            // Commission
            if (!Schema::hasColumn('courses', 'commission_type')) {
                $table->enum('commission_type', ['fixed', 'percent'])
                      ->default('fixed');
            }

            if (!Schema::hasColumn('courses', 'commission_value')) {
                $table->decimal('commission_value', 10, 2)
                      ->default(0);
            }

            // Certificate
            if (!Schema::hasColumn('courses', 'certificate_enabled')) {
                $table->boolean('certificate_enabled')
                      ->default(false);
            }

            if (!Schema::hasColumn('courses', 'certificate_type')) {
                $table->string('certificate_type')
                      ->nullable();
            }

            if (!Schema::hasColumn('courses', 'completion_percentage')) {
                $table->integer('completion_percentage')
                      ->nullable();
            }

            if (!Schema::hasColumn('courses', 'quiz_required')) {
                $table->boolean('quiz_required')
                      ->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {

            // Restore base_price safely
            if (!Schema::hasColumn('courses', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable();
            }

            // Drop columns safely
            $columns = [
                'website_price',
                'affiliate_price',
                'discount_type',
                'discount_value',
                'commission_type',
                'commission_value',
                'certificate_enabled',
                'certificate_type',
                'completion_percentage',
                'quiz_required',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
