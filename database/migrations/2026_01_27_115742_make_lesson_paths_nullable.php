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
        Schema::table('lessons', function (Blueprint $table) {
            // In dono ko nullable banana zaroori hai kyunki ye insert ke baad update hote hain
            $table->string('video_path')->nullable()->change();
            $table->string('hls_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            //
        });
    }
};
