<?php

namespace App\Observers;

use App\Models\Doctor;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;

class DoctorObserver
{
    /**
     * Handle the Doctor "created" event.
     */
    public function created(Doctor $doctor): void
    {
        $doctor->recordActivity('created');

        $doctor->user->assignRole('doctor');
        $doctor->recordActivity('assigned role doctor');

        // $subscriptionService = new SubscriptionService();
        // $plan = SubscriptionPlan::find(1);
        // $subscription = $subscriptionService->subscribe($doctor, $plan);
        // $doctor->recordActivity('subscribed to ' . $plan->name . ' plan on ' . $subscription->starts_at);
    }

    /**
     * Handle the Doctor "updated" event.
     */
    public function updated(Doctor $doctor): void
    {
        $doctor->recordActivity('updated');
    }

    /**
     * Handle the Doctor "deleted" event.
     */
    public function deleted(Doctor $doctor): void
    {
        $doctor->recordActivity('deleted');
        $doctor->user->removeRole('doctor');
    }

    /**
     * Handle the Doctor "restored" event.
     */
    public function restored(Doctor $doctor): void
    {
        $doctor->user->assignRole('doctor');
        $doctor->recordActivity('restored');
    }

    /**
     * Handle the Doctor "force deleted" event.
     */
    public function forceDeleted(Doctor $doctor): void
    {
        $doctor->user->removeRole('doctor');
        $doctor->recordActivity('force deleted');
    }
}
