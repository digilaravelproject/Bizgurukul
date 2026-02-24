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
        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('document_path')->nullable()->after('upi_id');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->after('document_path');
            $table->text('admin_note')->nullable()->after('status');
            $table->timestamp('verified_at')->nullable()->after('admin_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->dropColumn(['document_path', 'status', 'admin_note', 'verified_at']);
        });
    }
};
