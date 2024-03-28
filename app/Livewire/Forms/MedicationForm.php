<?php

namespace App\Livewire\Forms;

use App\Models\Medication;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MedicationForm extends Form
{
    #[Validate('required|string|max:255')]
    public $medication_name;

    #[Validate('required')]
    public $dosage;

    #[Validate('required')]
    public $frequency;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $encounter_id;

    public function store()
    {
        $this->validate();

        Medication::create([
            'medication_name' => $this->medication_name,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'notes' => $this->notes,
            'encounter_id' => $this->encounter_id,
            'patient_id' => $this->patient_id,
        ]);
    }

    public function empty()
    {
        $this->medication_name = '';
        $this->dosage = '';
        $this->frequency = '';
        $this->notes = '';
    }

    public function setEncounterId($encounter_id)
    {
        $this->encounter_id = $encounter_id;
    }

    public function setPatientId($patient_id)
    {
        $this->patient_id = $patient_id;
    }
}
