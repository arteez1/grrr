<?php

namespace App\Services;

use App\Models\VkCollection;
use VK\Client\VKApiClient;
use Illuminate\Support\Facades\Log;

class VkCollectionService
{
    public function __construct(
        private VKApiClient $vk
    ) {}

    public function createCollection(VkCollection $collection, int $groupId, string $accessToken): void
    {

        try {
            $response = $this->vk->market()->addAlbum($accessToken, [
                'owner_id' => -$groupId,
                'title' => $collection->title,
                'is_main' => false,
            ]);

            $collection->updateQuietly([ // updateQuietly чтобы избежать рекурсии
                'vk_collection_id' => $response['market_album_id'],
                'synced_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('VK Collection create failed', [
                'error' => $e->getMessage(),
                'collection' => $collection->id
            ]);
            throw $e;
        }

    }

    public function updateCollection(VkCollection $collection, int $groupId, string $accessToken): void
    {
        try {
            $this->vk->market()->editAlbum($accessToken, [
                'owner_id' => -$groupId,
                'album_id' => $collection->vk_collection_id,
                'title' => $collection->title,
            ]);

            $collection->updateQuietly(['synced_at' => now()]);

        } catch (\Throwable $e) {
            Log::error('VK Collection update failed', [
                'error' => $e->getMessage(),
                'collection' => $collection->id
            ]);
            throw $e;
        }
    }
}
