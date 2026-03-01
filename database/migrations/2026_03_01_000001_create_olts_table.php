<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('olts')) {
            Schema::create('olts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
                $table->string('name', 150);
                $table->string('ip_address', 45)->nullable();
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->string('snmp_community')->nullable();
                $table->string('snmp_version')->nullable();
                $table->enum('status', ['active','inactive','maintenance'])->default('active');
                $table->timestamps();

                $table->index('tenant_id');
                $table->index('ip_address');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('olts');
    }
};
