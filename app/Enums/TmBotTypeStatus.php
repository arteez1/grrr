<?php

namespace App\Enums;

enum TmBotTypeStatus: string
{
    // Типы ботов
    case TYPE_ADMIN = 'admin';
    case TYPE_CUSTOMER = 'client';
    case TYPE_MANAGER = 'manager';

    public function getLabel(): string
    {
        return match($this) {
            self::TYPE_ADMIN => 'Админ',
            self::TYPE_CUSTOMER => 'Клиент',
            self::TYPE_MANAGER => 'Менеджер',
        };
    }
    public static function getOptions(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->getLabel(), self::cases())
        );
    }
}
