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
        Schema::table('mikrotik_profiles', function (Blueprint $table) {
            $table->foreign('customer_import_request_id')->references('id')->on('customer_imports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mikrotik_profiles', function (Blueprint $table) {
            $table->dropForeign(['customer_import_request_id']);
        });
    }
};
