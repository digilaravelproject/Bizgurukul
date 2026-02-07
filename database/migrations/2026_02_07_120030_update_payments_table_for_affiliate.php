<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();

            if (!Schema::hasColumn('payments', 'bundle_id')) {
                $table->foreignId('bundle_id')->nullable()->after('course_id')->constrained('bundles')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
             // We can't easily revert nullable change without raw SQL depending on DB,
             // but we can drop bundle_id
             $table->dropForeign(['bundle_id']);
             $table->dropColumn('bundle_id');
             // $table->unsignedBigInteger('course_id')->nullable(false)->change(); // This might fail if nulls exist
        });
    }
};
