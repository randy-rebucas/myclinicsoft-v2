<?php

namespace App\Observers;

use App\Models\Receptionist;

class ReceptionistObserver
{
    /**
     * Handle the Receptionist "created" event.
     */
    public function created(Receptionist $receptionist): void
    {
        $receptionist->user->assignRole('receptionist');
    }

    /**
     * Handle the Receptionist "updated" event.
     */
    public function updated(Receptionist $receptionist): void
    {
        // $receptionist->user->assignRole('receptionist');
    }

    /**
     * Handle the Receptionist "deleted" event.
     */
    public function deleted(Receptionist $receptionist): void
    {
        $receptionist->user->removeRole('receptionist');
    }

    /**
     * Handle the Receptionist "restored" event.
     */
    public function restored(Receptionist $receptionist): void
    {
        $receptionist->user->assignRole('receptionist');
    }

    /**
     * Handle the Receptionist "force deleted" event.
     */
    public function forceDeleted(Receptionist $receptionist): void
    {
        $receptionist->user->removeRole('receptionist');
    }
}
