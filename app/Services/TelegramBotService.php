<?php

namespace App\Services;

use App\Models\TmBot;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\WebhookInfo;

class TelegramBotService
{
    private Api $telegram;
    private TmBot $bot;

    public function __construct(int $botId)
    {
        try {
            $this->bot = TmBot::findOrFail($botId);
            $this->telegram = new Api($this->bot->token);
        } catch (TelegramSDKException $e) {
            logger()->error("Failed service for bot {$botId}: " . $e->getMessage());
        }
    }

    // Установка вебхука
    public function setWebhook(string $url): bool
    {
        try {
            $this->telegram->setWebhook(['url' => $url, 'allowed_updates' => ['message', 'callback_query']]);
            $this->bot->update(['webhook_url' => $url]);
            return true;
        } catch (TelegramSDKException $e) {
            logger()->error("Telegram setWebhook error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Обработка входящего обновления
     */
    public function handleUpdate(array $update): void
    {
        app(TelegramCommandService::class, ['bot' => $this->bot])->handle($update);
        /*if (isset($update['message']['text'])) {
            $command = explode(' ', $update['message']['text'])[0]; // Берём первую часть (/start)
            $this->handleCommand($command, $update['message']);
        }*/
    }

    public function registerCommands(): void
    {
        $commands = $this->bot->commands->pluck('handler_method', 'command')->toArray();
        $this->telegram->addCommands(array_map(fn($handler)=>app($handler), $commands));
    }

    public function getBot(): TmBot
    {
        return $this->bot;
    }

    // Проверяет текущий вебхук и обновляет его, если URL изменился
    public function syncWebhook(string $url): bool
    {
        try {
            $currentWebhook = $this->getWebhookInfo();

            // Если вебхук уже установлен и совпадает — пропускаем
            if ($currentWebhook['url'] === $url) {
                return true;
            }
            // Если URL другой — обновляем
            return $this->setWebhook($url);
        } catch (TelegramSDKException $e) {
            logger()->error("Telegram webhook sync failed: " . $e->getMessage());
            return false;
        }
    }

    // Удаление вебхука
    public function deleteWebhook(): bool
    {
        try {
            $this->telegram->deleteWebhook();
            $this->bot->update(['webhook_url' => null]);
            return true;
        } catch (TelegramSDKException $e) {
            logger()->error("Telegram deleteWebhook error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @return WebhookInfo|null
     */
    // Получить информацию вебхука
    public function getWebhookInfo(): ?WebhookInfo
    {
        try {
            return $this->telegram->getWebhookInfo();
        } catch (TelegramSDKException $e) {
            return null;
        }
    }


    // Обработка входящих команд
    public function handleCommand(string $command, array $message): void
    {
        $command = $this->bot->commands()
            ->where('command', $command)
            ->where('is_active', true)
            ->first();

        if ($command && method_exists($this, $command->handler_method)) {
            $this->{$command->handler_method}($message);
        }
    }
    // Пример обработчика команды /start
    public function handleStartCommand(array $message): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $message['chat']['id'],
            'text' => 'Добро пожаловать!',
        ]);
    }
}
