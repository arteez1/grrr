<?php

namespace App\Jobs;

use App\Services\Temp\TelegramAdminService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAdminNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramAdminService $telegramAdminService): void
    {
        $telegramAdminService->sendOrderAlert($this->order);
    }
}
