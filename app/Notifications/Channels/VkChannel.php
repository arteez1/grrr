<?php

namespace App\Notifications\Channels;

use App\Services\VkMessengerService;

class VkChannel
{
    public function __construct(
        private VkMessengerService $vk
    ) {}

    public function send($notifiable, $notification): void
    {
        $message = $notification->toVkOrderStatus($notifiable);

        $messageId = $this->vk->sendMessage(
            $notifiable->vk_id,
            $message['text'],
            $message['buttons'] ?? []
        );

        if ($notification instanceof \App\Notifications\Notification) {
            $notification->model->update([
                'vk_message_id' => $messageId
            ]);
        }
    }
}
