<?php

namespace App\Notifications\Channels;

use App\Models\Notification;
use App\Services\TelegramNotificationService;
use Illuminate\Notifications\Notification as LaravelNotification;
class TelegramChannel
{
    public function __construct(
        private TelegramNotificationService $telegram
    ) {}

    public function send($notifiable, LaravelNotification $notification): void
    {
        $message = $notification->toTelegramOrderStatus($notifiable);

        $response = $this->telegram->sendMessage([
            'chat_id' => $notifiable->telegram_id,
            'text' => $message['text'],
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => $message['buttons']
            ])
        ]);

        // Сохраняем ID сообщения для последующего обновления
        if ($notification instanceof \App\Notifications\Notification) {
            $notification->model->update([
                'telegram_message_id' => $response->getMessageId()
            ]);
        }
    }
}
