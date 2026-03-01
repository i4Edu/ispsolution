<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('onus')) {
            Schema::create('onus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('olt_id')->nullable()->constrained('olts')->nullOnDelete();
                $table->string('serial_number')->index();
                $table->string('mac_address')->nullable();
                $table->string('pon_port')->nullable();
                $table->string('onu_type')->nullable();
                $table->enum('status', ['active','inactive','fault'])->default('active');
                $table->timestamps();

                $table->unique(['olt_id','serial_number']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('onus');
    }
};
