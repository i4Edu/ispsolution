<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete(); // Added admin_id
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete(); // Renamed from user_id
            $table->decimal('amount', 10, 2);
            $table->string('method'); // Renamed from payment_method
            $table->timestamps();

            $table->index(['tenant_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
