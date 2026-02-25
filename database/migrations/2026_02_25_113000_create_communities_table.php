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
        Schema::create('communities', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->string('description')->nullable();
            $col->string('link')->nullable();
            $col->string('button_text')->default('Join Now');
            $col->string('group_name'); // 'Join Our Communities', 'Join Our Social Media Channels'
            $col->boolean('is_active')->default(true);
            $col->boolean('is_custom')->default(false);
            $col->integer('order_index')->default(0);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
