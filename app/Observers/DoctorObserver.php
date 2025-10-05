<?php

namespace App\Observers;

use App\Models\Doctor;

class DoctorObserver
{
    /**
     * Handle the Doctor "created" event.
     */
    public function created(Doctor $doctor): void
    {
        $doctor->recordActivity('created');

        if ($doctor->user) {
            $doctor->user->assignRole('doctor');
            $doctor->recordActivity('assigned role doctor');
        }

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
        if ($doctor->user) {
            $doctor->user->removeRole('doctor');
        }
    }

    /**
     * Handle the Doctor "restored" event.
     */
    public function restored(Doctor $doctor): void
    {
        if ($doctor->user) {
            $doctor->user->assignRole('doctor');
        }
        $doctor->recordActivity('restored');
    }

    /**
     * Handle the Doctor "force deleted" event.
     */
    public function forceDeleted(Doctor $doctor): void
    {
        if ($doctor->user) {
            $doctor->user->removeRole('doctor');
        }
        $doctor->recordActivity('force deleted');
    }
}
