<?php
namespace App\Services;

use App\Enums\TagStatus;
use App\Events\VkSyncFailed;
use App\Models\Product;
use App\Models\VkProductMetadata;
use Throwable;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VkProductSyncService
{
    private VKApiClient $vk;
    private int $groupId;
    private string $accessToken;
    public function __construct()
    {
        $this->vk = new VKApiClient();
        $this->groupId = (int) config('services.vk.group_id');
        $this->accessToken = config('services.vk.access_token');
    }

    /**
     * Основной метод синхронизации товара
     */
    public function syncProduct(Product $product, bool $forceSync = false): void
    {

        // Синхронизируем подборки
        try {
            if (!$this->shouldSync($product, $forceSync)) {
                return;
            }

            $this->validateProduct($product);

            $params = $this->prepareProductData($product);

            $metadata = $product->vkMetadata;

            if ($metadata?->vk_product_id) {
                $this->updateProduct($product, $params);
            } else {
                $this->createProduct($product, $params);
            }

            $this->syncVkCollections($product);
            $this->syncRelatedProducts($product);

        } catch (VKApiException $e) {
            $this->handleApiError($product, $e);
        } catch (Throwable $e) {
            Log::error('VK sync failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Подготовка данных товара для VK API
     */
    private function prepareProductData(Product $product): array
    {
        $metadata = $product->vkMetadata;

        $tags = $product->tags()
            ->where('type', TagStatus::TYPE_VK)
            ->pluck('name')
            ->map(fn ($name) => "#{$name}")
            ->toArray();

        return [
            'owner_id' => -$this->groupId,
            'name' => $product->name,
            'description' => $this->formatDescription($product),
            'price' => $product->price,
            'old_price' => $product->old_price ?: null,
            'sku' => $product->sku,
            //'dimensions' => "{$metadata->width}x{$metadata->height}x{$metadata->depth}",
            'width' => $metadata->width,
            'height' => $metadata->height,
            'length' => $metadata->depth,
            'weight' => $metadata->weight,
            'main_photo_id' => $this->uploadImage($product->vk_image),
            'availability' => $metadata->availability,
            'category_id' => $this->resolveVkCategoryId($product),
            'album_ids' => $product->vkCollections->pluck('vk_collection_id')->toArray(),
            'keywords' => implode(', ', $tags)
        ];
    }

    /**
     * Создание нового товара в VK
     */
    private function createProduct(Product $product, array $params): void
    {
        $response = $this->vk->market()->add($this->accessToken, $params);

        VkProductMetadata::updateOrCreate(
            ['product_id' => $product->id],
            [
                'vk_product_id' => $response['market_item_id'],
                'synced_at' => now()
            ]
        );

        Log::info('VK product created', [
            'product_id' => $product->id,
            'vk_product_id' => $response['market_item_id']
        ]);
    }

    /**
     * Обновление существующего товара
     */
    private function updateProduct(Product $product, array $params): void
    {
        $params['item_id'] = $product->vkMetadata->vk_product_id;
        $this->vk->market()->edit($this->accessToken, $params);

        $product->vkMetadata->update(['synced_at' => now()]);

        Log::debug('VK product updated', [
            'product_id' => $product->id,
            'vk_product_id' => $params['item_id']
        ]);
    }

    /**
     * Синхронизация сопутствующих товаров
     */
    private function syncRelatedProducts(Product $product): void
    {
        $relatedIds = $product->relatedItems
            ->pluck('vkMetadata.vk_product_id')
            ->filter()
            ->take(10)
            ->toArray();

        if (empty($relatedIds)) {
            return;
        }

        $this->vk->market()->addToAlbum($this->accessToken, [
            'owner_id' => -$this->groupId,
            'item_id' => $product->vkMetadata->vk_product_id,
            'album_ids' => $relatedIds
        ]);

        $product->vkMetadata->update([
            'related_vk_product_ids' => $relatedIds
        ]);
    }

    /**
     * Форматирование описания с учетом related products
     */
    private function formatDescription(Product $product): string
    {
        $description = $product->description;

        if ($product->relatedItems->isNotEmpty()) {
            $description .= "\n\nС этим товаром покупают:";
            foreach ($product->relatedItems as $item) {
                $description .= sprintf(
                    "\n- %s (арт. %s)",
                    $item->name,
                    $item->sku
                );
            }
        }

        return mb_substr($description, 0, 1000); // Лимит VK
    }

    /**
     * Валидация перед синхронизацией
     */
    private function validateProduct(Product $product): void
    {
        if (!$product->vkMetadata) {
            throw new \RuntimeException('Отсутствуют метаданные VK для товара');
        }

        if ($product->relatedItems->count() > 10) {
            throw new \RuntimeException('Не более 10 сопутствующих товаров');
        }
    }

    /**
     * Нужно ли синхронизировать товар
     */
    private function shouldSync(Product $product, bool $forceSync): bool
    {
        return $forceSync ||
            !$product->vkMetadata?->synced_at ||
            $product->updated_at->greaterThan($product->vkMetadata->synced_at);
    }

    /**
     * Загрузка изображения в VK
     */
    private function uploadImage(string $imagePath): string
    {
        $cacheKey = "vk_image_hash_" . md5_file($imagePath);

        return Cache::remember($cacheKey, now()->addDay(), function() use ($imagePath) {
            $uploadServer = $this->vk->photos()->getMarketUploadServer(
                $this->accessToken,
                ['group_id' => $this->groupId]
            );

            // Реализация загрузки файла на сервер VK
            $photo = $this->uploadToVk($uploadServer['upload_url'], $imagePath);

            $response = $this->vk->photos()->saveMarketPhoto($this->accessToken, [
                'group_id' => $this->groupId,
                'photo' => $photo['photo'],
                'server' => $photo['server'],
                'hash' => $photo['hash']
            ]);

            return $response[0]['id'];
        });
    }

    /**
     * Обработка ошибок VK API
     */
    private function handleApiError(Product $product, VKApiException $e): void
    {
        Log::error('VK API error', [
            'product_id' => $product->id,
            'error_code' => $e->getErrorCode(),
            'error_msg' => $e->getMessage()
        ]);

        // Автоматический retry для определенных ошибок
        if (in_array($e->getErrorCode(), [6, 10, 29])) {
            throw $e;
        }

        // Специфичная обработка ошибок подборок
        if ($e->getErrorCode() === 100) { // "Album not found"
            $product->vkCollections()->detach();
            Log::warning('VK album not found, reset local collections');
        }

        // Уведомление админа
        event(new VkSyncFailed($product, $e->getMessage()));
    }

    /**
     * Определяет ID категории в VK
     */
    private function resolveVkCategoryId(Product $product): ?int
    {
        $category = $product->categories()
            ->whereHas('vkMappings')
            ->with('vkMappings')
            ->first();

        return $category?->vkMappings?->first()?->vk_category_id;
    }

    /**
     * Синхронизирует подборки товара с VK
     */
    public function syncVkCollections(Product $product): void
    {
        if (!$product->vkMetadata?->vk_product_id) return;

        $vk = new VKApiClient();

        $currentAlbums = Cache::remember(
            "product:{$product->id}:vk_collections",
            now()->addHour(),
            fn() => $product->vkCollections->pluck('vk_collection_id')->toArray()
        );

        try {
            // Получаем текущие подборки товара в VK
            $response = $vk->market()->getAlbums($this->accessToken, [
                'owner_id' => -$this->groupId,
                'item_id' => $product->vkMetadata->vk_product_id,
                'count' => 100
            ]);

            $vkAlbums = $response['items'] ?? [];

            // Удаляем из подборок, которых нет в текущих связях
            foreach (array_diff($vkAlbums, $currentAlbums) as $albumId) {
                $vk->market()->removeFromAlbum($this->accessToken, [
                    'owner_id' => -$this->groupId,
                    'item_id' => $product->vkMetadata->vk_product_id,
                    'album_ids' => [$albumId]
                ]);
            }

            // Добавляем в новые подборки
            foreach (array_diff($currentAlbums, $vkAlbums) as $albumId) {
                $vk->market()->addToAlbum($this->accessToken, [
                    'owner_id' => -$this->groupId,
                    'item_id' => $product->vkMetadata->vk_product_id,
                    'album_ids' => [$albumId]
                ]);
            }

            $product->touch(); // Обновляем метку времени товара

        } catch (\Throwable $e) {
            Log::error('VK collections sync failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function processTags(Product $product, array $tags, int $groupId, string $token): void
    {
        // Добавление хэштегов к товару
        // ...
    }
}
