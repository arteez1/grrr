<?php

namespace App\Services;

use App\Models\Order;
use Telegram\Bot\Api;
class TelegramOrderService
{
    public function __construct(
        private Api $telegram
    ) {}

    public function notifyNewOrder(Order $order): void
    {
        $message = $this->formatOrderMessage($order);

        $response = $this->telegram->sendMessage([
            'chat_id' => config('services.telegram.admin_chat_id'),
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);

        $order->update(['tm_message_id' => $response->getMessageId()]);
    }

    private function formatOrderMessage(Order $order): string
    {
        $items = $order->items->map(fn ($item) => (
        "{$item->product->name} - {$item->quantity} × {$item->price_at_purchase} ₽"
        ))->implode("\n");

        return "<b>Новый заказ #{$order->id}</b>\n\n"
            . "Клиент: {$order->client->full_name}\n"
            . "Сумма: {$order->total_amount} ₽\n"
            . "Способ доставки: {$order->delivery_method}\n\n"
            . "<u>Товары:</u>\n{$items}";
    }
}
