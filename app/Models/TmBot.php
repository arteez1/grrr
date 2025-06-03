<?php

namespace App\Models;

use App\Services\TelegramBotService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TmBot extends Model
{
    protected $table = 'tm_bots';

    protected $fillable = [
        'name',
        'token',
        'type',
        'settings',
        'webhook_secret',
        'webhook_url',
        'is_active'
    ];

    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    // Связь с командами бота
    public function commands(): HasMany
    {
        return $this->hasMany(TmCommand::class, 'bot_id');
    }

    public function getBotService(): TelegramBotService
    {
        return app(TelegramBotService::class, ['botId' => $this->id]);
    }

    public function setupWebhook()
    {
        $service = $this->getBotService();
    }
}
