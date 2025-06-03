<?php

namespace App\Services\Temp;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramAdminService
{
    private Api $bot;
    private string $adminChatId;

    public function __construct()
    {
        $this->bot = new Api(config('services.telegram.admin_bot_token'));
        $this->adminChatId = config('services.telegram.admin_chat_id');
    }

    /**
     * Редактирует товар через команду бота
     */
    public function handleEditProductCommand(int $productId, array $changes): string
    {
        try {
            $product = Product::findOrFail($productId);
            $product->update($changes);

            return "Товар #{$productId} обновлен: " . json_encode($changes);
        } catch (\Exception $e) {
            return "Ошибка: " . $e->getMessage();
        }
    }

    /**
     * Уведомление админам о новом заказе.
     */
    public function notifyAdmin(Order $order): void
    {
        try {
            $message = "🛒 Новый заказ #{$order->id}\n";
            $message .= "Клиент: {$order->client->name}\n";
            $message .= "Сумма: {$order->total_amount} руб.";

            $this->telegram->sendMessage([
                'chat_id' => $this->adminChatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram notifyAdmin error: " . $e->getMessage());
        }
    }

}
