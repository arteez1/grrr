<?php

namespace App\Listeners;

use App\Events\VkCollectionCreated;
use App\Events\VkCollectionUpdated;
use App\Jobs\SyncVkCollectionJob;
use Illuminate\Support\Facades\Log;

class SyncVkCollection
{

    public function handle(VkCollectionCreated|VkCollectionUpdated $event): void
    {
        try {
            // Отправляем задачу в очередь
            SyncVkCollectionJob::dispatch($event->collection)
                ->onQueue('vk-sync');

        } catch (\Throwable $e) {
            Log::error('Failed to dispatch VK sync job', [
                'error' => $e->getMessage(),
                'collection_id' => $event->collection->id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
