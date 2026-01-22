<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable(); // Ex: UP, MH, DL
            $table->string('country_code')->default('IN'); // Future proofing
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('states');
    }
};
