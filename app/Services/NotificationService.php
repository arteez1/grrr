<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\Temp\TelegramAdminService;

class NotificationService
{
    public function __construct(private TelegramAdminService $telegramAdminService)
    {
        //
    }

    public function createNotification(User $user, string $type, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function notifyAdmins(string $message): void
    {
        $this->telegramAdminService->sendToAdmins($message);
    }
}
