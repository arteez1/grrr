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
        Schema::create('vk_collections', function (Blueprint $table) {
            $table->id();
            $table->integer('vk_collection_id')->unique()->comment('ID подборки в VK');
            $table->string('title')->comment('Название подборки');
            $table->boolean('is_active')->default(true);
            $table->timestamp('synced_at')->nullable()->comment('Дата последней синхронизации с VK');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vk_collections');
    }
};
