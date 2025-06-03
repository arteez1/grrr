<?php

namespace App\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class TelegramMessageService
{
    private Api $telegram;

    public function __construct(string $token)
    {
        $this->telegram = new Api($token);
    }

    /**
     * Отправка сообщения
     */
    public function sendMessage(int $chatId,string $text,array $buttons = [],string $parseMode = 'HTML'): ?Message
    {
        try {
            $params = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => $parseMode,
            ];

            if(!empty($buttons)){
                $params['reply_markup'] = $this->buildKeyboard($buttons);
            }

            return $this->telegram->sendMessage($params);
        } catch (TelegramSDKException $e){
            report($e);
            return null;
        }
    }

    /**
     * Редактирование сообщения
     */
    public function editMessage(int $chatId,int $messageId,string $text,array $buttons = []): bool
    {

        try {
            $params = [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ];

            if(!empty($buttons)){
                $params['reply_markup'] = $this->buildKeyboard($buttons);
            }

            $this->telegram->editMessageText($params);
            return true;
        } catch (TelegramSDKException $e){
            report($e);
            return false;
        }




    }

    /**
     * Генерация клавиатуры
     */
    private function buildKeyboard(array $buttons): string
    {
        /*return json_encode([
            'inline_keyboard' => array_map(function ($button) {
                return [[
                    'text' => $button['text'],
                    'url' => $button['url']
                ]];
            }, $buttons)
        ]);*/

        return json_encode(['inline_keyboard' => array_chunk($buttons, 2)]);
    }
}
