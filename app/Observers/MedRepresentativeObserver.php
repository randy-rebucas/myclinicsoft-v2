<?php

namespace App\Observers;

use App\Models\MedRepresentative;

class MedRepresentativeObserver
{
    /**
     * Handle the MedRepresentative "created" event.
     */
    public function created(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->assignRole('medrep');
        $medRepresentative->recordActivity('created');
    }

    /**
     * Handle the MedRepresentative "updated" event.
     */
    public function updated(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->recordActivity('updated');
    }

    /**
     * Handle the MedRepresentative "deleted" event.
     */
    public function deleted(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->removeRole('medrep');
        $medRepresentative->recordActivity('deleted');
    }

    /**
     * Handle the MedRepresentative "restored" event.
     */
    public function restored(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->assignRole('medrep');
        $medRepresentative->recordActivity('restored');
    }

    /**
     * Handle the MedRepresentative "force deleted" event.
     */
    public function forceDeleted(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->removeRole('medrep');
        $medRepresentative->recordActivity('force deleted');
    }
}
