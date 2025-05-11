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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('work_order_number');
            $table->text('failure');
            $table->text('private_comment')->nullable();
            $table->text('comment')->nullable();
            $table->text('physical_condition');
            $table->text('humidity');
            $table->text('test');
            $table->boolean('is_warranty')->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('device_id')->constrained('devices');
            $table->foreignId('closure_id')->nullable()->constrained('closures');
            $table->foreignId('repair_time_id')->constrained('repair_times');
            $table->foreignId('store_id')->constrained('stores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
