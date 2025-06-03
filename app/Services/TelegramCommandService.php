<?php

namespace App\Services;

use App\Models\TmBot;
use App\Models\TmCommand;
use Telegram\Bot\Objects\Update;

class TelegramCommandService
{
    private TmBot $bot;
    public function __construct(TmBot $bot)
    {
        $this->bot = $bot;
    }

    public function handle(Update $update): void
    {
        if ($update->isType('message')){
            $this->processCommand($update->getMessage());
        }
    }

    private function processCommand($message): void
    {
        $command = TmCommand::where('bot_id', $this->bot->id)->where('command', $message->getText())->first();

        if ($command){
            $handler = app($command->handler_method);
            $handler->handle($message, $this->bot);
        }
    }
}
