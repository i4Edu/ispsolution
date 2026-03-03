<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('nas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('ip_address')->nullable()->index();
            $table->string('secret')->nullable();
            $table->string('model')->nullable()->index();
            $table->string('api_type')->nullable()->default('routeros');
            $table->text('credentials')->nullable();
            $table->json('meta')->nullable();
            // treat routers as NAS (single source-of-truth)
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nas');
    }
};
