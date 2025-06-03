<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    public function view(User $user, DatabaseNotification $notification): bool
    {
        $order = Order::find($notification->data['order_id']);
        return $order && $order->client_id == $user->client->id;
    }
}
