<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Coupon Packages: Rename columns, add tracking
        Schema::table('coupon_packages', function (Blueprint $table) {

            // Rename price -> selling_price
            if (Schema::hasColumn('coupon_packages', 'price')) {
                $table->renameColumn('price', 'selling_price');
            } else if (!Schema::hasColumn('coupon_packages', 'selling_price')) {
                 $table->decimal('selling_price', 10, 2)->default(0)->after('description');
            }

            // Rename discount_price -> discount_value
            if (Schema::hasColumn('coupon_packages', 'discount_price')) {
                $table->renameColumn('discount_price', 'discount_value');
            } else if (!Schema::hasColumn('coupon_packages', 'discount_value')) {
                 $table->decimal('discount_value', 10, 2)->default(0)->after('selling_price');
            }

            // Indexes
            if (!collect(DB::select("SHOW INDEXES FROM coupon_packages"))->pluck('Key_name')->contains('coupon_packages_is_active_index')) {
                 $table->index('is_active');
            }
        });

        // 2. Coupons: Add Ownership, Package Link, Transfer Info
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index(); // Owner (Affiliate)
            }
            if (!Schema::hasColumn('coupons', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('user_id')->index();
            }
            if (!Schema::hasColumn('coupons', 'transferred_from')) {
                $table->unsignedBigInteger('transferred_from')->nullable()->after('user_id'); // If transferred from another user
            }
            if (!Schema::hasColumn('coupons', 'purchased_at')) {
                $table->timestamp('purchased_at')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('coupons', 'status')) {
                 $table->string('status')->default('active')->after('is_active')->index(); // active, used, expired
            }

            // Indexes
            $table->index('code');
            $table->index('expiry_date');
        });

        // 3. Coupon Transfers Table
        if (!Schema::hasTable('coupon_transfers')) {
            Schema::create('coupon_transfers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('coupon_id')->index();
                $table->unsignedBigInteger('from_user_id')->index();
                $table->unsignedBigInteger('to_user_id')->index();
                $table->timestamp('transferred_at')->useCurrent();
                $table->timestamps();

                $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
                $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_transfers');

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'package_id', 'transferred_from', 'purchased_at', 'status']);
            $table->dropIndex(['code']);
            $table->dropIndex(['expiry_date']);
        });

        Schema::table('coupon_packages', function (Blueprint $table) {
             if (Schema::hasColumn('coupon_packages', 'selling_price')) {
                $table->renameColumn('selling_price', 'price');
            }
            if (Schema::hasColumn('coupon_packages', 'discount_value')) {
                $table->renameColumn('discount_value', 'discount_price');
            }
            $table->dropIndex(['is_active']);
        });
    }
};
