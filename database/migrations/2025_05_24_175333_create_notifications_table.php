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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Тип события: order_created, review_pending и т.д.');
            $table->morphs('notifiable');
            $table->json('data')->nullable()->comment('Дополнительные данные (ID заказа, ссылки)');
            $table->string('telegram_message_id')->nullable();
            $table->integer('vk_message_id')->nullable();
            $table->timestamp('read_at')->nullable();
            //$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
