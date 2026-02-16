<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id'); // Adding admin_id
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('upload_speed')->nullable(); // Renamed from bandwidth_up
            $table->integer('download_speed')->nullable(); // Renamed from bandwidth_down
            $table->decimal('price', 10, 2);
            $table->string('billing_cycle')->default('monthly');
            $table->integer('validity_days')->nullable();
            $table->enum('billing_type', ['daily', 'monthly', 'onetime'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
        });
        Schema::dropIfExists('packages');
    }
};
