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
            $table->nullableMorphs('paymentable'); // Adds paymentable_type and paymentable_id

            // Make existing FKs nullable if they aren't already (usually good practice when moving to polymorphic)
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->unsignedBigInteger('bundle_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropMorphs('paymentable');
            // Revert isn't strictly necessary or easy without knowing original state,
            // but we can leave them nullable.
        });
    }
};
