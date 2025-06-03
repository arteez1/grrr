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
        Schema::create('vk_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('vk_article_id')->nullable()->comment('ID статьи в VK');
            $table->string('short_url')->nullable()->comment('Сокращенная ссылка');
            $table->integer('vk_post_id')->nullable()->comment('ID связанного поста в VK');
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('user_id')->default(1)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vk_articles');
    }
};
