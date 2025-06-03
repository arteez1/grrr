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
        Schema::create('tm_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained('tm_bots')->cascadeOnDelete();
            $table->string('command')->unique(); // Например, "/start"
            $table->string('handler_method'); // Например, "handleStartCommand"
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tm_commands');
    }
};
