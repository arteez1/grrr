<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpamFilter extends Model
{
    protected $fillable = ['type', 'value', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Типы фильтров
    public const TYPES = [
        'keyword' => 'Ключевое слово',
        'ip' => 'IP-адрес',
        'user_id' => 'ID пользователя',
        'regex' => 'Регулярное выражение'
    ];

    // Типы фильтров (константы для удобства)
    public const TYPE_KEYWORD = 'keyword';
    public const TYPE_IP = 'ip';
    public const TYPE_USER_ID = 'user_id';

    public static function getTypes(): array
    {
        return [
            self::TYPE_KEYWORD => 'По ключевому слову',
            self::TYPE_IP => 'По IP-адресу',
            self::TYPE_USER_ID => 'По ID пользователя',
        ];
    }
}
