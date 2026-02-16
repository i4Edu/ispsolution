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
        Schema::create('mikrotik_ip_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_import_request_id');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('nas_id')->constrained('nas')->onDelete('cascade'); // Changed from router_id to nas_id, constrained to nas
            $table->string('name')->index();
            $table->string('ranges'); // Changed from json to string
            $table->timestamps();

            $table->unique(['nas_id', 'name', 'customer_import_request_id']); // Updated unique constraint
            $table->index('tenant_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_ip_pools');
    }
};
