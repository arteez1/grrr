<?php

namespace App\Enums;

enum CategoryStatus: string
{
    case CAT_PRODUCT = 'product';
    case CAT_POST = 'post';
    case CAT_NEWS = 'news';

    public function getLabel(): string
    {
        return match($this) {
            self::CAT_PRODUCT => 'Продукты',
            self::CAT_POST => 'Статьи',
            self::CAT_NEWS => 'Новости',
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
