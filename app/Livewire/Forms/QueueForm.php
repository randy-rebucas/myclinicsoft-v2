<?php

namespace App\Livewire\Forms;

use App\Models\Department;
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
        $fullQueueNumber = $departmentCode . date('ymd') . $queueNumber;

        Queue::create([
            'patient_id' => $this->patient_id,
            'department_id' => $this->department_id,
            'queue_number' => $fullQueueNumber,
            'priority' => $this->priority,
            'notes' => $this->notes,
        ]);

        $this->reset();
    }
}
