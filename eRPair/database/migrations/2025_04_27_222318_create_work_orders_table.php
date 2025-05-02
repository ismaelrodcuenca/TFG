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
            $table->text('failure');
            $table->text('private_comment')->nullable();
            $table->text('comment')->nullable();
            $table->text('physical_condition');
            $table->text('test');
            $table->boolean('is_warranty')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('handed_by')->nullable()->constrained('users');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('work_order_closure_id')->constrained('work_order_closures');
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
