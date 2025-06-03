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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_published_vk')->default(false);
            $table->boolean('is_published_tm')->default(false);
            $table->string('main_image')->nullable()->comment('Основное изображение для сайта');
            $table->string('vk_image')->nullable()->comment('Оптимизированное для VK');
            $table->string('tm_image')->nullable()->comment('Оптимизированное для Telegram');

            //$table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            //$table->foreignId('user_id')->constrained();
            $table->timestamps();

            $table->index('is_published');
            $table->index('is_published_vk');
            $table->index('is_published_tm');
        });


        //Промежуточные таблицы
        Schema::create('product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'category_id']);
        });

        Schema::create('product_tag', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'tag_id']);
        });

        Schema::create('product_vk_collection', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vk_collection_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'vk_collection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
