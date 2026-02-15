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
        Schema::create('mikrotik_ppp_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_import_request_id')->constrained('customer_import_requests')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('nas_id')->constrained('nas')->onDelete('cascade');
            $table->string('name');
            $table->string('local_address')->nullable();
            $table->string('remote_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_ppp_profiles');
    }
};
