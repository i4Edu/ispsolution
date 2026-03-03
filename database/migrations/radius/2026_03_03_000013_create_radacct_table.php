<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('radius')->create('radacct', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->index();
            $table->string('acctsessionid')->nullable()->index();
            $table->timestamp('acctstarttime')->nullable()->index();
            $table->timestamp('acctstoptime')->nullable()->index();
            $table->integer('acctsessiontime')->nullable();
            $table->string('acctterminatecause')->nullable()->index();
            $table->string('nasipaddress')->nullable()->index();
            $table->string('framedipaddress')->nullable()->index();
            $table->string('callingstationid')->nullable()->index();
            $table->string('acctuniqueid')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->unsignedBigInteger('operator_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('radius')->dropIfExists('radacct');
    }
};
