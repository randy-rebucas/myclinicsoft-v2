<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

trait RecordsActivity
{
    /**
     * Get all activities for the model.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Record an activity for the model.
     *
     * @param string $type
     * @param string|null $description
     * @return void
     */
    public function recordActivity($type, $description = null)
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description ?? "Record was {$type}",
            'changes' => $this->getChanges(),
            'causer_id' => Auth::user()?->id
        ]);
    }
}
