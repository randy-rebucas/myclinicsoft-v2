<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the Patient "created" event.
     */
    public function created(User $user): void
    {

    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(User $user): void
    {
        $user->recordActivity('updated');
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(User $user): void
    {
        $user->recordActivity('deleted');
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(User $user): void
    {
        $user->recordActivity('restored');
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $user->recordActivity('force deleted');
    }
}
