<?php

namespace App\Jobs;

use App\Models\DoctorSubscription;
use App\Notifications\SubscriptionRenewed;
use App\Notifications\SubscriptionRenewalFailed;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSubscriptionRenewal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private DoctorSubscription $subscription
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        if (!$this->subscription->auto_renew) {
            return;
        }

        try {
            // Create new payment intent
            $paymentIntent = $paymentService->createPaymentIntent($this->subscription);

            // Extend subscription period
            $this->subscription->update([
                'starts_at' => $this->subscription->ends_at,
                'ends_at' => $this->subscription->billing_period === 'monthly'
                    ? $this->subscription->ends_at->addMonth()
                    : $this->subscription->ends_at->addYear(),
            ]);

            // Send notification to doctor
            $this->subscription->doctor->notify(new SubscriptionRenewed($this->subscription));
        } catch (\Exception $e) {
            report($e);
            $this->subscription->doctor->notify(new SubscriptionRenewalFailed($this->subscription));
        }
    }
}
