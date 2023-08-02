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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->decimal('quantity', 10, 2);
            $table->uuid('unit_of_measurement_id');
            $table->foreign('unit_of_measurement_id')->references('id')->on('unit_of_measurements');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['activo', 'inactivo', 'pendiente']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};


