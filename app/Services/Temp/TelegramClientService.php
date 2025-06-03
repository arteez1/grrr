<?php

namespace App\Services\Temp;

use App\Models\Client;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramClientService
{
    private Api $bot;
    private string $clientChatId;

    public function __construct()
    {
        $this->bot = new Api(config('services.telegram.client_bot_token'));
        $this->clientChatId = config('services.telegram.client_chat_id');
    }


    /**
     * Отправляет список товаров клиенту
     */
    public function sendProductList(int $chatId): void
    {
        try {
            $products = Product::where('is_published', true)->get();

            foreach ($products as $product) {
                $this->sendProductCard($chatId, $product);
            }
        } catch (TelegramSDKException $e) {
            Log::error("Telegram product list error: " . $e->getMessage());
        }
    }
    /**
     * Отправляет карточку товара с кнопками
     */
    public function sendProductCard(int $chatId, Product $product): void
    {
        try {
            $buttons = [
                [
                    ['text' => '🛒 Заказать', 'callback_data' => "order_{$product->id}"],
                    ['text' => 'ℹ Подробнее', 'url' => route('product.show', $product->slug)]
                ]
            ];
            $this->bot->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $product->tm_image,
                'caption' => "{$product->name}\nЦена: {$product->price} руб.",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $buttons
                ]),
                'parse_mode' => 'HTML'
            ]);
        } catch (TelegramSDKException $e) {
            logger()->error("Telegram product card error: " . $e->getMessage());
        }
    }

    /**
     * Обрабатывает команду /start
     */
    public function handleStartCommand(int $chatId, Client $client): void
    {
        try {
            $text = "Добро пожаловать, {$client->first_name}!\n\n";
            $text .= "Используйте команды:\n";
            $text .= "/products - Список товаров\n";
            $text .= "/orders - Ваши заказы";

            $this->bot->sendMessage([
                'chat_id' => $chatId,
                'text' => $text
            ]);
        } catch (TelegramSDKException $e) {
            Log::error("Telegram start command error: " . $e->getMessage());
        }
    }
    /**
     * Уведомление клиенту о статусе заказа.
     */
    public function notifyClient(Client $client, string $text): void
    {
        if (!$client->telegram_user_id) return;

        try {
            $this->telegram->sendMessage([
                'chat_id' => $client->telegram_user_id,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram notifyClient error: " . $e->getMessage());
        }
    }
    public function syncClientToTelegram(Client $client): void
    {
        try {
            $telegram = new Api(config('services.telegram.bot_token'));

            $telegram->sendMessage([
                'chat_id' => $client->telegram_user_id,
                'text' => "Добро пожаловать, {$client->first_name}! Ваш аккаунт зарегистрирован.",
            ]);
        } catch (TelegramSDKException $e) {

        }
    }

    /**
     * Отправляет уведомление о статусе заказа
     */
    public function sendOrderNotification(Client $client, Order $order): void
    {
        try {
            $statuses = [
                'pending' => '⏳ Ваш заказ #%d в обработке',
                'completed' => '✅ Заказ #%d готов к выдаче!',
                'cancelled' => '❌ Заказ #%d отменен'
            ];

            $text = sprintf($statuses[$order->status], $order->id);
            $text .= "\nСумма: {$order->total_amount} руб.";

            $this->bot->sendMessage([
                'chat_id' => $client->telegram_user_id,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
        } catch (TelegramSDKException $e) {
            Log::error("Telegram order notification error: " . $e->getMessage());
        }
    }
    /**
     * Публикует пост в канал
     */
    public function publishToChannel(Post $post): void
    {
        try {
            $this->bot->sendPhoto([
                'chat_id' => $this->channelId,
                'photo' => $post->telegram_image,
                'caption' => $this->generatePostCaption($post),
                'parse_mode' => 'HTML'
            ]);
        } catch (TelegramSDKException $e) {
            Log::error("Telegram channel post error: " . $e->getMessage());
        }
    }
    private function generateProductCaption(Product $product): string
    {
        return "<b>{$product->name}</b>\n\n"
            . "💰 Цена: <b>{$product->price} руб.</b>\n"
            . ($product->old_price ? "❌ Старая цена: <s>{$product->old_price} руб.</s>\n" : "")
            . "📦 Остаток: {$product->stock_quantity} шт.\n\n"
            . "{$product->short_description}";
    }
    private function generatePostCaption(Post $post): string
    {
        return "<b>{$post->title}</b>\n\n"
            . Str::limit(strip_tags($post->content), 200) . "...\n\n"
            . "🔗 Читать полностью: " . route('post.show', $post->slug);
    }

    public function handleDiscountCommand($update): void
    {
        $chatId = $update->getChat()->getId();
        $client = Client::where('telegram_id', $chatId)->first();

        if (!$client) {
            $this->sendMessage($chatId, 'Сначала зарегистрируйтесь!');
            return;
        }

        $promoCode = trim($update->getMessage()->getText());
        $promoCode = str_replace('/promo', '', $promoCode);

        $discount = app(DiscountService::class)->applyDiscountToCart($client->cart, $promoCode);

        if ($discount) {
            $message = "✅ Промокод применен!\nСкидка: {$discount->formatted_amount}";
        } else {
            $message = "❌ Промокод недействителен или истек";
        }

        $this->sendMessage($chatId, $message);
    }
}
