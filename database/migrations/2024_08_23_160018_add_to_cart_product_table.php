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
        Schema::table('cart_product', function (Blueprint $table) {
            //
            $table->foreignId('cart_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_product', function (Blueprint $table) {
            //
        });
    }
};
