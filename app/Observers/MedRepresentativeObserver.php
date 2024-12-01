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
        $medRepresentative->user->assignRole('med-representative');
    }

    /**
     * Handle the MedRepresentative "updated" event.
     */
    public function updated(MedRepresentative $medRepresentative): void
    {
        // $medRepresentative->user->assignRole('med-representative');
    }

    /**
     * Handle the MedRepresentative "deleted" event.
     */
    public function deleted(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->removeRole('med-representative');
    }

    /**
     * Handle the MedRepresentative "restored" event.
     */
    public function restored(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->assignRole('med-representative');
    }

    /**
     * Handle the MedRepresentative "force deleted" event.
     */
    public function forceDeleted(MedRepresentative $medRepresentative): void
    {
        $medRepresentative->user->removeRole('med-representative');
    }
}
