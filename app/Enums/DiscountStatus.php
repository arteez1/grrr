<?php

namespace App\Enums;

enum DiscountStatus: string
{
    case TYPE_PERCENTAGE   = 'percentage';
    case TYPE_FIXED   = 'fixed';

    public function getLabel(): string
    {
        return match($this) {
            self::TYPE_PERCENTAGE  => 'Скидка в %',
            self::TYPE_FIXED  => 'Фиксированная',
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
