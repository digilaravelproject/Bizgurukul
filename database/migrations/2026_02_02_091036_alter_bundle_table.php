<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bundles')) {
            Schema::create('bundles', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('thumbnail')->nullable();
                $table->boolean('is_published')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('bundles', function (Blueprint $table) {
                if (!Schema::hasColumn('bundles', 'slug')) {
                    $table->string('slug')->unique()->after('title');
                }
            });
        }

        Schema::dropIfExists('bundle_course');

        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_id');
            $table->morphs('item');
            $table->integer('order_column')->default(0);
            $table->timestamps();
            $table->foreign('bundle_id')->references('id')->on('bundles')->onDelete('cascade');
            $table->index(['bundle_id', 'item_id', 'item_type']);
            $table->unique(['bundle_id', 'item_id', 'item_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('bundles');
    }
};
