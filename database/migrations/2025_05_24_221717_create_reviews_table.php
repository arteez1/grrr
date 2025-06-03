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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            // Полиморфные поля
            $table->unsignedBigInteger('reviewable_id');
            $table->string('reviewable_type'); // Сохраняет класс модели (например, "App\Models\Product")

            // Дополнительное поле для типа отзыва (например, "product", "service")
            $table->string('type')->comment('Тип отзыва: product, post, general');

            $table->text('content');
            $table->integer('rating')->between(1, 5);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Индексы
            $table->index(['reviewable_id', 'reviewable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
