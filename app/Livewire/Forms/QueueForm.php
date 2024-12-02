<?php

namespace App\Livewire\Forms;

use App\Events\QueueUpdated;
use App\Models\Department;
use App\Models\Patient;
use App\Models\Queue;
use Livewire\Attributes\Validate;
use Livewire\Form;

class QueueForm extends Form
{
    #[Validate('required')]
    public $priority;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $department_id;

    public function store()
    {
        $this->validate();


        $lastQueue = Queue::where('department_id', $this->department_id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $queueNumber = $lastQueue ? sprintf('%03d', intval(substr($lastQueue->queue_number, -3)) + 1) : '001';

        $departmentCode = Department::find($this->department_id)->code;
        $fullQueueNumber = $departmentCode . $queueNumber;

        Queue::create([
            'patient_id' => $this->patient_id,
            'department_id' => $this->department_id,
            'queue_number' => $fullQueueNumber,
            'priority' => $this->priority,
            'notes' => $this->notes,
        ]);


        $patient = Patient::find($this->patient_id);
        $patient->recordActivity('queue_created', "Added to queue {$fullQueueNumber} in {$departmentCode} department");

        broadcast(new QueueUpdated("Queue {$fullQueueNumber} has been added!", 'new'))->toOthers();

        $this->reset();
    }
}
