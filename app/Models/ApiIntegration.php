<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = ['name', 'credentials', 'is_active'];
    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
    ];

    // Типы интеграций
    public const VK_API = 'vk_api';
    public const TELEGRAM_API = 'telegram_api';

    public static function getVkApiToken(): ?string
    {
        return static::where('name', self::VK_API)
            ->value('credentials->token');
    }

    public static function getTelegramApiToken(): ?string
    {
        return static::where('name', self::TELEGRAM_API)
            ->value('credentials->token');
    }
}
