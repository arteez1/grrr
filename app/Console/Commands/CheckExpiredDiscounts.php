<?php

namespace App\Console\Commands;

use App\Models\Discount;
use Illuminate\Console\Command;

class CheckExpiredDiscounts extends Command
{
    protected $signature = 'discounts:check-expired';
    protected $description = 'Deactivate expired discounts';

    public function handle(): void
    {
        $count = Discount::where('is_active', true)
            ->where('end_date', '<', now())
            ->update(['is_active' => false]);

        $this->info("Deactivated {$count} expired discounts.");

        if ($count > 0) {
            // Можно добавить уведомление в Telegram
            // Notification::send(...);
        }
    }
}
