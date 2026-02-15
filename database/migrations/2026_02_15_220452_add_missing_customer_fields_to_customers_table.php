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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('password');
            $table->foreignId('billing_profile_id')->nullable()->after('package_id')->constrained('billing_profiles')->onDelete('set null');
            $table->timestamp('package_expired_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['billing_profile_id']);
            $table->dropColumn(['mobile', 'billing_profile_id', 'package_expired_at']);
        });
    }
};
