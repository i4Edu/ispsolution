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
        Schema::create('nas', function (Blueprint $table) {
            $table->id();
            $table->string('nasname');
            $table->string('shortname');
            $table->string('type')->default('mikrotik');
            $table->string('secret');
            $table->string('api_username');
            $table->string('api_password');
            $table->integer('api_port')->default(8728);
            $table->string('community')->default('billing');
            $table->string('description')->nullable();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Assuming a tenants table exists
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Assuming 'users' is the admin table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nas');
    }
};
