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
        Schema::create('cash_desk_closures', function (Blueprint $table) {
            $table->id();
            $table->double('cash_float');
            $table->double('cash_amount');
            $table->double('card_amount');
            $table->double('measured_cash_amount');
            $table->double('measured_card_amount');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_desk_closures');
    }
};
