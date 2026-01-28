<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kyc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pan_name');
            $table->string('document_path');
            $table->string('document_type')->default('pan_card');
            $table->text('admin_note')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('upi_id')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_details');
        Schema::dropIfExists('kyc_details');
    }
};
