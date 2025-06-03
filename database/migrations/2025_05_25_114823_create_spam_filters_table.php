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
        Schema::create('spam_filters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('Тип: keyword, ip, user_id, regex');
            $table->string('value')->comment('Значение фильтра'); // Например, "реклама" или "192.168.1.1"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type'); // Для быстрого поиска
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spam_filters');
    }
};
