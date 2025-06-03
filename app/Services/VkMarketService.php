<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\Product;
use App\Models\VkCollection;
use VK\Client\VKApiClient;
use Illuminate\Support\Facades\Log;

class VkMarketService
{
    private VKApiClient $client;
    private string $accessToken;
    private int $groupId;

    public function __construct()
    {
        $this->client = new VKApiClient();
        $this->accessToken = ApiIntegration::getVkApiToken();
        $this->groupId = abs(config('services.vk.group_id')); // ID группы (отрицательное число)
    }

    /**
     * Публикует товар в VK Market и подборки.
     */
    public function publishProduct(Product $product, array $collectionIds = []): void
    {
        try {
            // Получаем метаданные для VK
            $metadata = $product->vkMetadata;

            // 1. Загружаем изображение
            $photoId = $this->uploadImage($product->vk_image ?? $product->main_image);

            // 2. Публикуем товар
            $response = $this->client->market()->add($this->accessToken, [
                'owner_id' => -$this->groupId,
                'name' => $product->name,
                'description' => $this->generateProductDescription($product),
                'price' => $product->price,
                'old_price' => $product->old_price,
                'main_photo_id' => $photoId,
                'category_id' => $metadata->vk_category_id,
                'width' => $metadata->width,
                'weight' => $metadata->weight,
                'depth' => $metadata->depth,
                //'album_ids' => $this->getCollectionIds($product),
            ]);

            $product->update(['vk_product_id' => $response['id']]);

            // 3. Добавляем в подборки (если указаны)
            if (!empty($collectionIds)) {
                $this->addToCollections($product->vk_product_id, $collectionIds);
            }

        } catch (\Exception $e) {
            Log::error("VK Market Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Генерирует описание товара с тегами.
     */
    private function generateProductDescription(Product $product): string
    {
        // Получаем теги для VK из таблицы Tags
        $tags = $product->tags->pluck('name')->map(fn ($tag) => "#{$tag}")->implode(' ');
        return "{$product->description}\n\n{$tags}";
    }

    /**
     * Загружает изображение в VK и возвращает photo_id.
     */
    private function uploadImage(string $imagePath): string
    {
        // 1. Получаем URL для загрузки
        $uploadUrl = $this->client->photos()->getMarketUploadServer($this->accessToken, [
            'group_id' => $this->groupId,
        ])['upload_url'];

        // 2. Загружаем файл
        $response = Http::asMultipart()
            ->attach('file', file_get_contents($imagePath), basename($imagePath))
            ->post($uploadUrl)
            ->json();

        // 3. Сохраняем фото в VK
        $photo = $this->client->photos()->saveMarketPhoto($this->accessToken, [
            'group_id' => $this->groupId,
            'photo' => $response['photo'],
            'server' => $response['server'],
            'hash' => $response['hash'],
        ]);

        return $photo[0]['id']; // Возвращаем ID загруженного изображения
    }

    /**
     * Добавляет товар в подборки VK.
     */
    private function addToCollections(int $productVkId, array $collectionIds): void
    {
        foreach ($collectionIds as $albumId) {
            $this->client->market()->addToAlbum($this->accessToken, [
                'owner_id' => -$this->groupId,
                'item_id' => $productVkId,
                'album_ids' => $albumId,
            ]);
        }
    }

    /**
     * Получаем подборки VK.
     */
    private function getCollectionIds(Product $product): string
    {
        return $product->vkCollections->pluck('vk_collection_id')->implode(',');
    }

    /**
     * Синхронизация подборки VK.
     */
    public function syncCollection(VkCollection $collection): void
    {
        $vk = new VKApiClient();
        $accessToken = config('services.vk.token');
        $groupId = config('services.vk.group_id');

        // Создать/обновить подборку в VK
        $params = [
            'owner_id' => -$groupId,
            'title' => $collection->title,
        ];

        if ($collection->vk_collection_id) {
            // Обновление существующей подборки
            $vk->market()->editAlbum($accessToken, $params + ['album_id' => $collection->vk_collection_id]);
        } else {
            // Создание новой подборки
            $response = $vk->market()->addAlbum($accessToken, $params);
            $collection->update(['vk_collection_id' => $response['id']]);
        }

        // Синхронизация товаров в подборке
        $productIds = $collection->products->pluck('vkMetadata.vk_product_id')->filter();
        $vk->market()->addToAlbum($accessToken, [
            'owner_id' => -$groupId,
            'album_id' => $collection->vk_collection_id,
            'item_ids' => implode(',', $productIds),
        ]);

        // После успешной синхронизации:
        $collection->update(['synced_at' => now()]);
    }

}
