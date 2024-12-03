<?php

namespace App\Observers;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientObserver
{
    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        $patient->user->assignRole('patient');
        $patient->recordActivity('created');
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        $patient->recordActivity('updated');
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        $patient->user->removeRole('patient');
        $patient->recordActivity('deleted');
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        $patient->user->assignRole('patient');
        $patient->recordActivity('restored');
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        $patient->user->removeRole('patient');
        $patient->recordActivity('force deleted');
    }
}
