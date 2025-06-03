<?php

namespace App\Enums;

enum OrderStatus: string
{
    case STATUS_PENDING  = 'pending';
    case STATUS_COMPLETED  = 'completed';
    case STATUS_CANCELLED  = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::STATUS_PENDING => 'В обработке',
            self::STATUS_COMPLETED => 'Выполнен',
            self::STATUS_CANCELLED => 'Отменен',
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
