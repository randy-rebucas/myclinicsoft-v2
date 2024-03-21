<?php

namespace App\Livewire\Forms;

use App\Models\PhysicalExamination;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PhysicalExaminationForm extends Form
{
    
    #[Validate('required|string|max:255')]
    public $general_appearance;
    
    #[Validate('required|string|max:255')]
    public $systematic_findings;
    
    #[Validate([
        'vital_signs.*.type' => [
            'required'
        ],
        'vital_signs.*.value' => [
            'required',
            'max:255'
        ],
    ])]
    public $vital_signs = [];

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $encounter_id;

    // Blood Glucose
    // Blood Pressure
    public function store()
    {
        $validated = $this->validate();
        PhysicalExamination::create($validated);
    }

    public function empty()
    {
        $this->medication_name = '';
        $this->dosage = '';
        $this->frequency = '';
        $this->notes = '';
    }
}
