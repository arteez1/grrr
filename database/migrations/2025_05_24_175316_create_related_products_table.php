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
        Schema::create('related_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')
                ->cascadeOnDelete();
            $table->foreignId('related_product_id')->constrained('products')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'related_product_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_products');
    }
};
