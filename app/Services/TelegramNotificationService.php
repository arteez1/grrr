<?php

namespace App\Services;

use App\Contracts\TelegramNotificationInterface;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService implements TelegramNotificationInterface
{
    public function __construct(
        private TelegramMessageService $messageService
    ) {}

    /**
     * Отправка уведомления пользователю
     */
    public function sendNotification(int $chatId,string $message,array $buttons = [],string $parseMode = 'HTML'): ?int
    {
        try {

            $response = $this->messageService->sendMessage(
                chatId: $chatId,
                text: $message,
                buttons: $buttons,
                parseMode: $parseMode
            );

            return $response->getMessageId();

        } catch (\Throwable $e) {
            Log::channel('telegram')->error('Notification failed', [
                'chat_id' => $chatId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Обновление существующего уведомления
     */
    public function updateNotification(int $chatId,int $messageId,string $newText,array $newButtons = []): bool
    {
        return $this->messageService->editMessage(
            chatId: $chatId,
            messageId: $messageId,
            text: $newText,
            buttons: $newButtons
        );
    }

    public function toTelegram(): array
    {
        return [];
    }
}
