<?php

namespace App\Observers;

use App\Models\Queue;

class QueueObserver
{
    /**
     * Handle the Queue "created" event.
     */
    public function created(Queue $queue): void
    {
        $queue->recordActivity('created');
    }

    /**
     * Handle the Queue "updated" event.
     */
    public function updated(Queue $queue): void
    {
        $queue->recordActivity('updated');
    }

    /**
     * Handle the Queue "deleted" event.
     */
    public function deleted(Queue $queue): void
    {
        $queue->recordActivity('deleted');
    }

    /**
     * Handle the Queue "restored" event.
     */
    public function restored(Queue $queue): void
    {
        $queue->recordActivity('restored');
    }

    /**
     * Handle the Queue "force deleted" event.
     */
    public function forceDeleted(Queue $queue): void
    {
        $queue->recordActivity('force deleted');
    }
}
