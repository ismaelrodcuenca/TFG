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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->double('taxes_full_amount');
            $table->integer('store_order_number')->nullable();
            $table->double('full_amount');
            $table->double('down_payment_amount')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('is_down_payment')->default(0);
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
