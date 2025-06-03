<?php

namespace App\Providers;

use App\Notifications\Channels\TelegramChannel;
use App\Notifications\Channels\VkChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('telegram', fn() => $this->app->make(TelegramChannel::class));
            $service->extend('vk', fn() => $this->app->make(VkChannel::class));
        });
    }
}
