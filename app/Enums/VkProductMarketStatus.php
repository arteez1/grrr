<?php

namespace App\Enums;

enum VkProductMarketStatus: int
{
    case AVAILABLE  = 0;
    case REMOVED  = 1;
    case UNAVAILABLE  = 2;

    public function getLabel(): string
    {
        return match($this) {
            self::AVAILABLE => 'Доступен',
            self::REMOVED => 'Скрыт',
            self::UNAVAILABLE => 'Нет в наличии',
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
