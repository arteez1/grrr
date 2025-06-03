<?php

namespace App\Jobs;

use App\Models\Discount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeactivateExpiredDiscountsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Discount::where('end_date', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
