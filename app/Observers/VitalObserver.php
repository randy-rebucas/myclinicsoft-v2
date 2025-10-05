<?php

namespace App\Observers;

use App\Models\Vital;

class VitalObserver
{
    /**
     * Handle the Vital "created" event.
     */
    public function created(Vital $vital): void
    {
        $vital->recordActivity('created');
    }

    /**
     * Handle the Vital "updated" event.
     */
    public function updated(Vital $vital): void
    {
        $vital->recordActivity('updated');
    }

    /**
     * Handle the Vital "deleted" event.
     */
    public function deleted(Vital $vital): void
    {
        $vital->recordActivity('deleted');
    }

    /**
     * Handle the Vital "restored" event.
     */
    public function restored(Vital $vital): void
    {
        $vital->recordActivity('restored');
    }

    /**
     * Handle the Vital "force deleted" event.
     */
    public function forceDeleted(Vital $vital): void
    {
        $vital->recordActivity('force deleted');
    }
}
