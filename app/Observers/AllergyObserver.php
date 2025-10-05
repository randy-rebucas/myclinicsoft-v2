<?php

namespace App\Observers;

use App\Models\Allergy;

class AllergyObserver
{
    /**
     * Handle the Allergy "created" event.
     */
    public function created(Allergy $allergy): void
    {
        $allergy->recordActivity('created');
    }

    /**
     * Handle the Allergy "updated" event.
     */
    public function updated(Allergy $allergy): void
    {
        $allergy->recordActivity('updated');
    }

    /**
     * Handle the Allergy "deleted" event.
     */
    public function deleted(Allergy $allergy): void
    {
        $allergy->recordActivity('deleted');
    }

    /**
     * Handle the Allergy "restored" event.
     */
    public function restored(Allergy $allergy): void
    {
        $allergy->recordActivity('restored');
    }

    /**
     * Handle the Allergy "force deleted" event.
     */
    public function forceDeleted(Allergy $allergy): void
    {
        $allergy->recordActivity('force deleted');
    }
}
