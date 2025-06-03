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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->text('short_content')->nullable()->comment('Для анонса в соцсетях');
            $table->enum('type', ['article', 'news']);
            $table->foreignId('vk_article_id')->nullable()->comment('Связь с статьей VK')->constrained('vk_articles')
                ->onDelete('set null');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_published_vk')->default(false);
            $table->boolean('is_published_tm')->default(false);
            $table->string('main_image')->nullable()->comment('Основное изображение для сайта');
            $table->string('vk_image')->nullable()->comment('Изображение для VK');
            $table->string('tm_image')->nullable()->comment('Изображение для Telegram');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
