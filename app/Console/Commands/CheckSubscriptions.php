<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Check subscription statuses and process renewals';

    public function handle(SubscriptionService $subscriptionService): void
    {
        $this->info('Checking subscriptions...');
        $subscriptionService->checkSubscriptionStatus();
        $this->info('Subscription check completed.');
    }
}
