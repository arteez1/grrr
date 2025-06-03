<?php

namespace App\Http\Controllers;

use App\Models\TmBot;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{

    public function handle(Request $request, TmBot $bot)
    {
        $service = new TelegramBotService($bot->id);
        $update = $request->all();

        $service->handleUpdate($update);

        return response()->json(['status' => 'ok']);
    }
}
