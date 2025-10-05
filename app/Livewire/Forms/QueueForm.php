<?php

namespace App\Livewire\Forms;

use App\Events\QueueUpdated;
use App\Models\ClinicDoctor;
use App\Models\Department;
use App\Models\Patient;
use App\Models\Queue;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Str;
use App\Enums\QueuePriorityEnum;

class QueueForm extends Form
{
    #[Validate('required|in:low,normal,high,urgent,emergency')]
    public $priority;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $clinic_id;

    public function store()
    {
        $this->validate();

        $lastQueue = Queue::where('clinic_id', $this->clinic_id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $queueNumber = $lastQueue ? sprintf('%03d', intval(substr($lastQueue->queue_number, -3)) + 1) : '001';

        $clinicDoctor = ClinicDoctor::with('clinic')->where('clinic_id', $this->clinic_id)->first();
        $clinicName = $clinicDoctor->clinic->name;

        $fullQueueNumber = Str::substr($clinicName, 0, 1) . $queueNumber;

        $queue = Queue::create([
            'patient_id' => $this->patient_id,
            'clinic_id' => $this->clinic_id,
            'queue_number' => $fullQueueNumber,
            'priority' => $this->priority,
            'notes' => $this->notes,
        ]);

        // Log queue creation activity (aligns with ActivitySeeder)
        $queue->recordActivity('created', 'Patient was added to queue');

        broadcast(new QueueUpdated("Queue {$fullQueueNumber} has been added!", 'new'))->toOthers();

        $this->reset();
    }

    /**
     * Update queue status to in_progress and log activity
     */
    public function callNext($queueId)
    {
        $queue = Queue::find($queueId);
        if ($queue) {
            $queue->update([
                'status' => 'in_progress',
                'called_at' => now(),
            ]);

            // Log queue status update activity (aligns with ActivitySeeder)
            $queue->recordActivity('updated', 'Patient was called');
        }
    }

    /**
     * Complete queue and log activity
     */
    public function complete($queueId)
    {
        $queue = Queue::find($queueId);
        if ($queue) {
            $queue->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Log queue completion activity (aligns with ActivitySeeder)
            $queue->recordActivity('updated', 'Appointment was completed');
        }
    }

    /**
     * Cancel queue and log activity
     */
    public function cancel($queueId)
    {
        $queue = Queue::find($queueId);
        if ($queue) {
            $queue->update(['status' => 'cancelled']);

            // Log queue cancellation activity
            $queue->recordActivity('updated', 'Queue status was updated');
        }
    }

    /**
     * Update queue status and log activity
     */
    public function updateStatus($queueId, $status)
    {
        $queue = Queue::find($queueId);
        if ($queue) {
            $queue->update(['status' => $status]);

            // Log queue status update activity (aligns with ActivitySeeder)
            $queue->recordActivity('updated', 'Queue status was updated');
        }
    }
}
