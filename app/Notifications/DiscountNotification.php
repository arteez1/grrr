<?php

namespace App\Notifications;

use App\Models\Discount;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DiscountNotification extends Notification
{
    use Queueable;

    public function __construct(public Discount $discount) {}

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): array
    {
        return [
            'text' => $this->formatMessage(),
            'buttons' => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Посмотреть товары',
                            'url' => route('products.index', ['discount' => $this->discount->code])
                        ]
                    ]
                ]
            ]
        ];
    }

    private function formatMessage(): string
    {
        $message = "🎉 Специальное предложение!\n";
        $message .= "Промокод: <b>{$this->discount->code}</b>\n";
        $message .= "Скидка: <b>{$this->discount->formatted_amount}</b>\n";

        if ($this->discount->end_date) {
            $message .= "Действует до: {$this->discount->end_date->format('d.m.Y')}";
        }

        return $message;
    }
}
