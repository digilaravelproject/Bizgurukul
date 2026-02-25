<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g. 'welcome', 'course_purchased'
            $table->string('name');          // Human-readable name
            $table->string('subject');       // Email subject line
            $table->longText('body');        // HTML body (template)
            $table->json('variables')->nullable(); // Available variables for this template
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
