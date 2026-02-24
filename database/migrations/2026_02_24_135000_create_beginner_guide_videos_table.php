<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('beginner_guide_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['foundation', 'growth', 'scale'])->index();
            $table->text('description')->nullable();
            $table->text('resources')->nullable();
            $table->string('video_path');
            $table->integer('order_column')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('beginner_guide_videos');
    }
};