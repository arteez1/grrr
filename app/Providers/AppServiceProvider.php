<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\TmBot;
use App\Observers\OrderObserver;
use App\Services\TelegramBotService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
    }
}
