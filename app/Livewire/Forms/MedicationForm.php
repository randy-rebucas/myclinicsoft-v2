<?php

namespace App\Livewire\Forms;

use App\Models\Medication;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MedicationForm extends Form
{
    #[Validate('required|array')]
    public $prescription_items;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $encounter_id;

    public function store()
    {
        $this->validate();

        $medication = Medication::create([
            'prescription_items' => $this->prescription_items,
            'notes' => $this->notes,
            'encounter_id' => $this->encounter_id,
            'patient_id' => $this->patient_id,
        ]);

        // Log medication creation activity (aligns with ActivitySeeder)
        $medication->recordActivity('created', 'Medication was prescribed');
    }

    public function empty()
    {
        $this->prescription_items = [];
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
