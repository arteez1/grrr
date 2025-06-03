<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification as LaravelNotification;

abstract class Notification extends LaravelNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Model $model) {}

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable->telegram_id) {
            $channels[] = 'telegram';
        }

        if ($notifiable->vk_id) {
            $channels[] = 'vk';
        }

        return $channels;
    }
}
