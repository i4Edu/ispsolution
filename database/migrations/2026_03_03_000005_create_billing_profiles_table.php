<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('billing_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('rules')->nullable();
            $table->unsignedTinyInteger('billing_cycle_days')->default(30);
            $table->boolean('is_prepaid')->default(true);
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_profiles');
    }
};
