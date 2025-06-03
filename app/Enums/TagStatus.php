<?php

namespace App\Enums;

enum TagStatus: string
{
    case TYPE_PRODUCT  = 'product';
    case TYPE_POST  = 'post';
    case TYPE_VK  = 'vk';

    public function getLabel(): string
    {
        return match($this) {
            self::TYPE_PRODUCT => 'Для товара',
            self::TYPE_POST => 'Для постов',
            self::TYPE_VK => 'Для ВК',
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
