<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('radius')->create('radreply', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->index();
            $table->string('attribute');
            $table->string('op')->default('=');
            $table->string('value')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('radius')->dropIfExists('radreply');
    }
};
