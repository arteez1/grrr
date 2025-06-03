<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;

class OrderStatusChanged extends Notification
{
    public function toTelegramOrderStatus($notifiable): array
    {
        return [
            'content' => $this->formatMessage(),
            'buttons' => [
                [
                    'text' => 'Посмотреть заказ',
                    'url' => route('orders.show', $this->model->id)
                ]
            ]
        ];
    }
    public function toVkOrderStatus($notifiable)
    {
        return [
            'text' => $this->formatMessage(),
            'buttons' => [
                [
                    'action' => [
                        'type' => 'open_link',
                        'label' => 'Открыть заказ',
                        'link' => route('orders.show', $this->model->id)
                    ]
                ]
            ]
        ];
    }

    private function formatMessage(): string
    {
        /** @var Order $order */
        $order = $this->model;

        return match($order->status) {
            OrderStatus::STATUS_COMPLETED =>
            "✅ Ваш заказ #{$order->id} завершен.\nСумма: {$order->total_amount} ₽",
            OrderStatus::STATUS_CANCELLED =>
            "❌ Заказ #{$order->id} отменен.\nПричина: {$order->cancellation_reason}",
            default =>
            "🔄 Заказ #{$order->id} получил новый статус: {$order->formatted_status}"
        };
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Статус вашего заказа изменен')
            ->line($this->formatMessage())
            ->action('Посмотреть заказ', route('orders.show', $this->order->id));
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => $this->formatMessage()
        ];
    }


}
