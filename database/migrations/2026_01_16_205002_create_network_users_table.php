<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('mobile')->nullable(); // Added mobile
            $table->enum('service_type', ['pppoe', 'hotspot', 'static'])->default('pppoe');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->foreignId('billing_profile_id')->nullable(); // Added billing_profile_id
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade'); // Renamed user_id to admin_id
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('package_expired_at')->nullable(); // Added package_expired_at
            $table->timestamps();

            $table->index('username');
            $table->index('service_type');
            $table->index('status');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_users');
    }
};
