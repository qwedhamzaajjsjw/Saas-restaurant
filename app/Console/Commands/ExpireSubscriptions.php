<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

/**
 * يُشغَّل يومياً عبر Scheduler:
 * يجد الاشتراكات التي انتهت موعدها ويضع حالتها على 'expired'.
 */
class ExpireSubscriptions extends Command
{
    protected $signature   = 'subscription:expire';
    protected $description = 'Mark overdue subscriptions as expired';

    public function handle(): int
    {
        $expired = Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No subscriptions to expire.');
            return self::SUCCESS;
        }

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);
            $this->line("  Expired subscription #{$subscription->id} (restaurant_id={$subscription->restaurant_id})");
        }

        $this->info("Marked {$expired->count()} subscription(s) as expired.");

        return self::SUCCESS;
    }
}
