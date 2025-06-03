<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\AutoResponse;
use App\Models\SpamFilter;
use App\Models\VkMessage;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;

/*Ответ на сообщения*/
class VkMessageService
{
    private VKApiClient $client;
    private string $accessToken;
    public function __construct()
    {
        $this->client = new VKApiClient();
        $this->accessToken = ApiIntegration::getVkApiToken();
    }

    /**
     * Отправляет сообщение пользователю VK.
     */
    public function sendMessage(int $userId, string $message, array $attachments = []): void
    {
        try {
            // Проверка на спам перед отправкой
            if ($this->isSpam($message)) {
                Log::warning("Сообщение пользователю $userId заблокировано спам-фильтром");
                return;
            }

            $params = [
                'user_id' => $userId,
                'message' => $message,
                'random_id' => rand(1, 1000000), // Обязательный параметр
                'attachment' => implode(',', $attachments),
            ];

            $this->client->messages()->send($this->accessToken, $params);

            // Логируем отправку
            VkMessage::create([
                'user_id' => $userId,
                'message' => $message,
                'direction' => 'out',
                'payload' => ['status' => 'sent'],
            ]);

        } catch (\Exception $e) {
            Log::error("VK Message Send Error: " . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Обрабатывает входящее сообщение (вызывается из контроллера вебхука).
     */
    public function processIncomingMessage(array $data): void
    {
        // Сохраняем сообщение в базу
        $this->saveMessage($data);
        //app(VkMarketService::class)->syncOrder($orderData);

        $this->checkTriggers($data);

        // Автоответ на простые сообщения
        if ($this->isGreeting($data['text'])) {
            $this->sendMessage($data['from_id'], "Привет! Чем могу помочь?");
        }
    }

    /**
     * Обработка нового заказа из VK Маркета.
     */
    public function processOrder(array $order): void
    {
        try {
            // Пример: создание заказа в вашей системе
            $orderData = [
                'vk_order_id' => $order['id'],
                'total_amount' => $order['price'] / 100, // Цена в рублях
                'client_id' => $order['customer']['id'],
            ];

            // Используйте сервис для синхронизации
            app(VkMarketService::class)->syncOrder($orderData);

        } catch (\Exception $e) {
            Log::error('Ошибка обработки заказа из VK', ['error' => $e->getMessage()]);
        }
    }

    private function checkTriggers(array $message): void
    {
        // Проверяем триггеры
        $autoResponses = AutoResponse::where('is_active', true)->get();
        $messageText = mb_strtolower($message['text']);

        foreach ($autoResponses as $response) {
            if ($this->isTooFrequent($message['from_id'])) {
                return;
            }
            if (str_contains($messageText, mb_strtolower($response->keyword))) {
                $this->sendResponse(
                    userId: $message['from_id'],
                    text: $response->response_text
                );
                break; // Отправляем только первый подходящий ответ
            }
        }
    }

    /**
     * Сохранение сообщения в БД для дальнейшего ответа через админку.
     */
    private function saveMessage(array $message): void
    {
        VkMessage::create([
            'user_id' => $message['from_id'],
            'message' => $message['text'],
            'direction' => 'in', // Входящее сообщение
            'payload' => $message,
        ]);
    }
    /**
     * Проверяет сообщение на спам.
     */
    private function isSpam(string $text): bool
    {
        return SpamFilter::where('type', 'keyword')
            ->where('value', 'like', "%$text%")
            ->exists();
    }

    /**
     * Определяет приветственные сообщения.
     */
    private function isGreeting(string $text): bool
    {
        $greetings = ['привет', 'здравствуйте', 'hi', 'hello'];
        $textLower = mb_strtolower($text);

        foreach ($greetings as $word) {
            if (str_contains($textLower, $word)) {
                return true;
            }
        }
        return false;
    }
}
