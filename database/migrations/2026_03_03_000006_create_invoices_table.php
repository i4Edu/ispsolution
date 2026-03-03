<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->string('currency')->default('BDT');
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['reference']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
