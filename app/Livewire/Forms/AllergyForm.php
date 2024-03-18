<?php

namespace App\Livewire\Forms;

use App\Models\Allergy;
use App\Models\Patient;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AllergyForm extends Form
{
    #[Validate('required|string|max:255')] 
    public $allergen;

    #[Validate('required|string|max:255')] 
    public $reaction;

    #[Validate('required')] 
    public $severity;

    #[Validate('max:3000')] 
    public $notes;

    #[Validate('required')] 
    public $patient_id;

    public Patient $patient;
    public function store()
    {
        $this->validate();

        Allergy::create([
            'allergen' => $this->allergen,
            'reaction' => $this->reaction,
            'severity' => $this->severity,
            'notes' => $this->notes,
            'patient_id' => $this->patient_id,
        ]);

        // $this->reset(); 
    }

    public function setPatientId(Patient $patient) {
        $this->patient = $patient;
        $this->patient_id = $this->patient->id;
    }
}
