<?php

namespace App\Services;

use App\Enums\DiscountStatus;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;

class DiscountService
{
    // Применение скидки к заказу
    public function applyDiscountToOrder(Order $order, string $discountCode): bool
    {
        $discount = Discount::where('code', $discountCode)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('used_count < max_uses');
            })
            ->first();

        if (!$discount) {
            return false;
        }

        // Проверка, что скидка применима к товарам в заказе
        if ($discount->products()->exists()) {
            $orderProductIds = $order->items->pluck('product_id');
            $discountProductIds = $discount->products->pluck('id');

            if ($orderProductIds->intersect($discountProductIds)->isEmpty()) {
                return false;
            }
        }

        $discountAmount = $this->calculateDiscountAmount($order, $discount);

        $order->update([
            'discount_id' => $discount->id,
            'total_amount' => $order->total_amount - $discountAmount
        ]);

        $discount->incrementUsage();

        return true;
    }

    // Расчет суммы скидки
    private function calculateDiscountAmount(Order $order, Discount $discount): float
    {
        if ($discount->type === DiscountStatus::TYPE_PERCENTAGE) {
            return $order->total_amount * ($discount->amount / 100);
        }

        return min($discount->amount, $order->total_amount);
    }

    // Получение цены товара со скидкой
    public function getDiscountedPrice(Product $product, ?Discount $discount = null): float
    {
        if (!$discount || !$discount->isAvailable()) {
            return $product->price;
        }

        if ($discount->products()->exists() && !$discount->products->contains($product)) {
            return $product->price;
        }

        return $discount->type === DiscountStatus::TYPE_PERCENTAGE
            ? $product->price * (1 - $discount->amount / 100)
            : max(0, $product->price - $discount->amount);
    }
}
