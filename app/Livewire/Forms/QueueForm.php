<?php

namespace App\Livewire\Forms;

use App\Models\Queue;
use Livewire\Attributes\Validate;
use Livewire\Form;

class QueueForm extends Form
{
    public $queue_number;

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

        Queue::create([
            'patient_id' => $this->patient_id,
            'department_id' => $this->department_id,
            'queue_number' => $this->queue_number,
            'priority' => $this->priority,
            'notes' => $this->notes
        ]);

        $this->reset();
    }

}
