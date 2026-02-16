<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            // Add payment_date column if it doesn't exist
            if (! Schema::hasColumn('customer_payments', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            if (Schema::hasColumn('customer_payments', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
        });
    }
};
