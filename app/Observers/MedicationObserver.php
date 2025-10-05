<?php

namespace App\Observers;

use App\Models\Medication;

class MedicationObserver
{
    /**
     * Handle the Medication "created" event.
     */
    public function created(Medication $medication): void
    {
        $medication->recordActivity('created');
    }

    /**
     * Handle the Medication "updated" event.
     */
    public function updated(Medication $medication): void
    {
        $medication->recordActivity('updated');
    }

    /**
     * Handle the Medication "deleted" event.
     */
    public function deleted(Medication $medication): void
    {
        $medication->recordActivity('deleted');
    }

    /**
     * Handle the Medication "restored" event.
     */
    public function restored(Medication $medication): void
    {
        $medication->recordActivity('restored');
    }

    /**
     * Handle the Medication "force deleted" event.
     */
    public function forceDeleted(Medication $medication): void
    {
        $medication->recordActivity('force deleted');
    }
}
