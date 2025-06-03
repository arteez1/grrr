<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function calculateTotal(Order $order): void
    {
        $total = $order->items->sum(function ($item) {
            return $item->quantity * $item->price_at_purchase;
        });

        // Применение скидки
        if ($order->discount && $order->discount->isActive()) {
            $total = match($order->discount->type) {
                'percentage' => $total * (1 - $order->discount->amount / 100),
                'fixed' => $total - $order->discount->amount,
            };
        }

        $order->update(['total_amount' => max($total, 0)]);
    }
}
