<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mikrotik_profiles')) {
            if (! Schema::hasColumn('mikrotik_profiles', 'tenant_id')) {
                Schema::table('mikrotik_profiles', function (Blueprint $table) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mikrotik_profiles') && Schema::hasColumn('mikrotik_profiles', 'tenant_id')) {
            Schema::table('mikrotik_profiles', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};
