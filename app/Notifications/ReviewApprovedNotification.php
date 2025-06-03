<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Telegram\Bot\Api;

class ReviewApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        return "Ваш отзыв на товар {$this->review->product->name} одобрен!";

        /*$telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $telegram->sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => "Уведомление: Рецензия одобрена!\nПользователь: {$notifiable->name}",
        ]);*/
    }
}
