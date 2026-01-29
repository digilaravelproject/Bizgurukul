<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('lessons', function (Blueprint $table) {
            $table->enum('type', ['video', 'document'])->default('video')->after('course_id')->index();
            $table->string('document_path')->nullable()->after('video_path');
            $table->string('thumbnail')->nullable()->after('title');

            $table->index('order_column');
        });
    }

    public function down() {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['order_column']);
            $table->dropColumn(['type', 'document_path', 'thumbnail']);
        });
    }
};
