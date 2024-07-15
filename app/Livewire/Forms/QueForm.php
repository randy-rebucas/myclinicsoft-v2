<?php

namespace App\Livewire\Forms;

use App\Models\Que;
use App\Models\Encounter;
use Livewire\Attributes\Validate;
use Livewire\Form;

class QueForm extends Form
{
    public $que_number;
    public $metadata;
    public $patient_id;
    public $status;
    public function store()
    {
        Que::create([
            'que_number' => $this->que_number,
            'metadata' => $this->metadata,
            'patient_id' => $this->patient_id,
            'status' => $this->status
        ]);

        $this->reset();
    }

    public function empty()
    {
        $this->que_number = '';
        $this->metadata = '';
        $this->patient_id = '';
        $this->status = '';
    }
}
