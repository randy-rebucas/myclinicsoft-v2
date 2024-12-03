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

class QueueForm extends Form
{
    #[Validate('required')]
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

        Queue::create([
            'patient_id' => $this->patient_id,
            'clinic_id' => $this->clinic_id,
            'queue_number' => $fullQueueNumber,
            'priority' => $this->priority,
            'notes' => $this->notes,
        ]);

        $patient = Patient::find($this->patient_id);
        $patient->recordActivity('queue_created', "Added to queue {$fullQueueNumber} in {$clinicName} clinic");

        broadcast(new QueueUpdated("Queue {$fullQueueNumber} has been added!", 'new'))->toOthers();

        $this->reset();
    }
}
