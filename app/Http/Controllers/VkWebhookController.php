<?php

namespace App\Http\Controllers;

use App\Services\VkMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VkWebhookController extends Controller
{
    public function handle(Request $request, VkMessageService $messageService)
    {
        $data = $request->all();

        // Проверка секретного ключа (обязательно!)
        if ($data['secret'] !== config('services.vk.callback_secret')) {
            Log::warning('Неверный секретный ключ VK', ['data' => $data]);
            abort(403);
        }

        switch ($data['type']) {
            case 'confirmation':
                return response(config('services.vk.confirmation_code'));

            case 'message_new':
                // Сохраните сообщение в БД для ответа через админку
                $messageService->processIncomingMessage($data['object']);
                break;

            case 'market_order_new':
                $messageService->processOrder($data['object']); // Синхронизация заказа
                break;

            case 'wall_post_new':
                // Обработка нового поста (если нужно)
                // Пример: обновить статус поста в БД
                Log::info('Новый пост на стене VK', $data);
                break;
        }

        return response()->json('ok');
    }

}
