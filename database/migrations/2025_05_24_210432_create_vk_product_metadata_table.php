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
        Schema::create('vk_product_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('vk_product_id')->nullable()->comment('ID товара в VK');
            $table->integer('width')->nullable()->comment('Ширина, мм');
            $table->integer('height')->nullable()->comment('Высота, мм');
            $table->integer('depth')->nullable()->comment('Глубина, мм');
            $table->integer('weight')->nullable()->comment('Вес, г');
            $table->tinyInteger('availability')
                ->default(0)
                ->comment('0-доступен, 1-скрыт, 2-недоступен');
            $table->json('vk_tags')
                ->nullable()
                ->comment('Хэштеги для VK');
            //$table->json('vk_collection_ids')->nullable()->comment('ID подборок VK');
            $table->timestamps();

            $table->unique('product_id');
            $table->index('vk_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vk_product_metadata');
    }
};
