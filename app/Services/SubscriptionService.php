<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\SubscriptionPlan;
use App\Models\DoctorSubscription;
use Carbon\Carbon;
use App\Jobs\ProcessSubscriptionRenewal;
use App\Notifications\SubscriptionExpiring;
use App\Notifications\PaymentSuccessful;
use App\Notifications\PaymentFailed;

class SubscriptionService
{
    public function subscribe(Doctor $doctor, SubscriptionPlan $plan): DoctorSubscription
    {
        // Cancel any active subscription
        $this->cancelActiveSubscription($doctor);

        // Create new subscription
        return $doctor->subscribe($plan);
    }

    public function cancelSubscription(DoctorSubscription $subscription): bool
    {
        return $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false
        ]);
    }

    private function cancelActiveSubscription(Doctor $doctor): void
    {
        $activeSubscription = $doctor->activeSubscription;

        if ($activeSubscription) {
            $this->cancelSubscription($activeSubscription);
        }
    }

    public function renewSubscription(DoctorSubscription $subscription): void
    {
        ProcessSubscriptionRenewal::dispatch($subscription);
    }

    public function checkSubscriptionStatus(): void
    {
        $expiringSubscriptions = DoctorSubscription::query()
            ->where('status', 'active')
            ->where('ends_at', '<=', now()->addDays(7))
            ->where('ends_at', '>', now())
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            $subscription->doctor->notify(new SubscriptionExpiring($subscription));

            if ($subscription->auto_renew) {
                $this->renewSubscription($subscription);
            }
        }
    }

    public function handlePaymentSuccess(DoctorSubscription $subscription): void
    {
        $subscription->update(['status' => 'active']);
        $subscription->doctor->notify(new PaymentSuccessful($subscription));
    }

    public function handlePaymentFailure(DoctorSubscription $subscription): void
    {
        $subscription->update(['status' => 'expired']);
        $subscription->doctor->notify(new PaymentFailed($subscription));
    }
}
