<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\Temp\VkMarketService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishProductToVkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Product $product) {}

    public function handle(VkMarketService $vkMarketService): void
    {
        $vkMarketService->publishProduct(
            product: $this->product,
            collectionIds: $this->product->vkCollections->pluck('id')->toArray()
        );
    }
}
