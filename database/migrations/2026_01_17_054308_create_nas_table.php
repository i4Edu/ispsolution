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
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Not nullable as per doc's general rule
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Adding admin_id
            $table->string('nasname'); // Renamed from nas_name
            $table->string('shortname'); // Renamed from short_name
            $table->string('type')->default('mikrotik'); // Changed default
            $table->string('secret');
            $table->string('api_username'); // Added
            $table->string('api_password'); // Added
            $table->integer('api_port')->default(8728); // Added
            $table->string('community')->default('billing'); // Changed default and made not nullable
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'nasname']); // Added unique constraint for tenant_id and nasname
            $table->index('tenant_id');
            $table->index('admin_id');
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
