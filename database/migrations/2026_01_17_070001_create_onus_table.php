<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade'); // Made not nullable
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Added admin_id
            $table->foreignId('olt_id')->constrained('olts')->onDelete('cascade');
            $table->string('pon_port', 50);
            $table->integer('onu_id');
            $table->string('serial_number', 100)->unique();
            $table->string('mac_address', 17)->nullable();
            $table->foreignId('network_user_id')->nullable()->constrained('network_users')->onDelete('set null');
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['online', 'offline', 'los', 'auth_failed', 'unknown'])->default('unknown');
            $table->decimal('signal_rx', 8, 2)->nullable();
            $table->decimal('signal_tx', 8, 2)->nullable();
            $table->integer('distance')->nullable();
            $table->string('ipaddress', 45)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();

            $table->index('olt_id');
            $table->index('tenant_id');
            $table->index('admin_id');
            $table->index('network_user_id');
            $table->index('serial_number');
            $table->index('status');
            $table->unique(['olt_id', 'pon_port', 'onu_id'], 'olt_pon_onu_unique');
        });
    }

    public function down(): void
    {
        Schema::table('onus', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            $table->dropForeign(['tenant_id']);
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade'); // Revert tenant_id to nullable
        });
        Schema::dropIfExists('onus');
    }
};
