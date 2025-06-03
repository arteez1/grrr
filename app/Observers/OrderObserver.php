<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use App\Services\TelegramOrderService;
use App\Services\VkOrderService;

class OrderObserver
{
    public function created(Order $order): void
    {
        app(TelegramOrderService::class)->notifyNewOrder($order);
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $order->client->notify(new OrderStatusChanged($order));
        }
    }
}
