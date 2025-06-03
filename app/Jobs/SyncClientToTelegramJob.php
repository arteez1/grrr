<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\TelegramClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncClientToTelegramJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Client $client)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramClientService $service): void
    {
        $service->syncClientToTelegram($this->client);
    }
}
