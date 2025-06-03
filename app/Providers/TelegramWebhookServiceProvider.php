<?php

namespace App\Providers;

use App\Models\TmBot;
use App\Services\TelegramBotService;
use Illuminate\Support\ServiceProvider;

class TelegramWebhookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->isProduction()) { // Регистрируем только в production
            $this->registerWebhooks();
        }
    }

    protected function registerWebhooks(): void
    {
        TmBot::where('is_active', true)->each(function (TmBot $bot) {
            try {
                $service = new TelegramBotService($bot);
                $url = route('telegram.webhook', ['bot' => $bot->id]);
                $service->syncWebhook($url); // <- Заменили setWebhook на syncWebhook
            } catch (\Exception $e) {
                logger()->error("Failed to set webhook for bot {$bot->id}: " . $e->getMessage());
            }
        });
    }
}
