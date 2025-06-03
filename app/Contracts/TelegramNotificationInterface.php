<?php

namespace App\Contracts;

interface TelegramNotificationInterface
{
    public function sendNotification(
        int $chatId,
        string $message,
        array $buttons = [],
        string $parseMode = 'HTML'
    ): ?int;

    public function updateNotification(
        int $chatId,
        int $messageId,
        string $newText,
        array $newButtons = []
    ): bool;

    public function toTelegram(): array;
}
