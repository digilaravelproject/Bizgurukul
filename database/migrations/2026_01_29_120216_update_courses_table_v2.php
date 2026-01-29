<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('id')->index();
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id')->index();

            $table->decimal('discount_value', 10, 2)->nullable()->after('price');
            $table->enum('discount_type', ['fixed', 'percent'])->nullable()->after('discount_value');
            $table->decimal('final_price', 10, 2)->default(0)->after('discount_type')->index();

            $table->boolean('certificate_enabled')->default(false)->after('is_published')->index();
            $table->enum('certificate_criteria', ['completion', 'quiz_pass'])->nullable()->after('certificate_enabled');
            $table->integer('completion_threshold')->default(100)->after('certificate_criteria');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'sub_category_id', 'discount_value', 'discount_type', 'final_price', 'certificate_enabled', 'certificate_criteria', 'completion_threshold']);
        });
    }
};
