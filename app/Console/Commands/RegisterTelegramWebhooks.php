<?php

namespace App\Console\Commands;

use App\Models\TmBot;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;

class RegisterTelegramWebhooks extends Command
{
    protected $signature = 'telegram:webhooks:register';
    protected $description = 'Register webhooks for active Telegram bots';

    public function handle(): int
    {
        TmBot::where('is_active', true)->each(function (TmBot $bot) {
            try {
                $service = new TelegramBotService($bot);
                $url = route('telegram.webhook', ['bot' => $bot->id]);
                $service->syncWebhook($url); // <- Заменили setWebhook на syncWebhook
                $this->info("Webhook set for bot: {$bot->name}");
            } catch (\Exception $e) {
                $this->error("Failed for bot {$bot->id}: " . $e->getMessage());
            }
        });

        return self::SUCCESS;
    }
}
