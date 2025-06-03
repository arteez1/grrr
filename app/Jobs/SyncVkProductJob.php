<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\VkProductSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SyncVkProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public Product $product) {}

    public function handle(VkProductSyncService $service): void
    {
        $service->syncProduct($this->product, true);
    }
}
