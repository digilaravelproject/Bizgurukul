<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Sirf wahi add hoga jo pehle se nahi hai
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
            }

            if (!Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('price');
            }

            if (!Schema::hasColumn('courses', 'demo_video_url')) {
                $table->string('demo_video_url')->nullable()->after('is_published');
            }

            if (!Schema::hasColumn('courses', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('title');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['price', 'is_published', 'demo_video_url', 'thumbnail']);
        });
    }
};
