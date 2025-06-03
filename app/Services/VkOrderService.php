<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use VK\Client\VKApiClient;

class VkOrderService
{
    public function __construct(
        private VKApiClient $vk
    ) {}

    public function syncOrder(Order $order): void
    {
        if ($order->vk_order_id) {
            $this->updateOrder($order);
        } else {
            $this->createOrder($order);
        }
    }

    private function createOrder(Order $order): void
    {
        $response = $this->vk->market()->createOrder(
            config('services.vk.access_token'),
            [
                'user_id' => $order->client->vk_user_id,
                'group_id' => config('services.vk.group_id'),
                'items' => $order->items->map(fn ($item) => [
                    'item_id' => $item->product->vkMetadata->vk_product_id,
                    'quantity' => $item->quantity
                ])->toArray()
            ]
        );

        $order->update(['vk_order_id' => $response['order_id']]);
    }

    private function updateOrder(Order $order): void
    {
        $this->vk->market()->changeOrderStatus(
            config('services.vk.access_token'),
            [
                'order_id' => $order->vk_order_id,
                'status' => $this->mapStatus($order->status),
                'group_id' => config('services.vk.group_id')
            ]
        );
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            OrderStatus::STATUS_COMPLETED => 'delivered',
            OrderStatus::STATUS_CANCELLED => 'cancelled',
            default => 'new'
        };
    }
}
