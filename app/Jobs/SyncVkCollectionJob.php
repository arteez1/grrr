<?php

namespace App\Jobs;

use App\Models\VkCollection;
use App\Services\VkCollectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncVkCollectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Количество попыток выполнения задания
     */
    public int $tries = 3;

    /**
     * Интервал между попытками (секунды)
     */
    public int $backoff = 60;

    //Запуск воркера для этой очереди:
    //php artisan queue:work --queue=vk-sync --tries=3 --delay=60
    //Для тестирования вручную:
    //SyncVkCollectionJob::dispatch(VkCollection::find(1));
    //Просмотр логов:
    //tail -f storage/logs/laravel.log
    //НУЖНО Уведомления в Telegram при критических ошибках
    public function __construct(public VkCollection $collection) {}

    public function handle(VkCollectionService $service): void
    {
        try {
            $groupId = config('services.vk.group_id');
            $token = config('services.vk.access_token');

            if (!$this->collection->vk_collection_id) {
                $service->createCollection($this->collection, $groupId, $token);
                Log::info('VK collection created', ['id' => $this->collection->id]);
            } else {
                $service->updateCollection($this->collection, $groupId, $token);
                Log::debug('VK collection updated', ['id' => $this->collection->id]);
            }

        } catch (Throwable $e) {
            Log::error('VK collection sync failed', [
                'collection_id' => $this->collection->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('VK collection sync job failed after retries', [
            'collection_id' => $this->collection->id,
            'error' => $exception->getMessage()
        ]);

        // Можно добавить уведомление админу через Telegram
        // Notification::send(...);
    }
}
