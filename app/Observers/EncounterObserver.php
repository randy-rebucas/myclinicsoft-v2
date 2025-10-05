<?php

namespace App\Observers;

use App\Models\Encounter;

class EncounterObserver
{
    /**
     * Handle the Encounter "created" event.
     */
    public function created(Encounter $encounter): void
    {
        $encounter->recordActivity('created');
    }

    /**
     * Handle the Encounter "updated" event.
     */
    public function updated(Encounter $encounter): void
    {
        $encounter->recordActivity('updated');
    }

    /**
     * Handle the Encounter "deleted" event.
     */
    public function deleted(Encounter $encounter): void
    {
        $encounter->recordActivity('deleted');
    }

    /**
     * Handle the Encounter "restored" event.
     */
    public function restored(Encounter $encounter): void
    {
        $encounter->recordActivity('restored');
    }

    /**
     * Handle the Encounter "force deleted" event.
     */
    public function forceDeleted(Encounter $encounter): void
    {
        $encounter->recordActivity('force deleted');
    }
}
