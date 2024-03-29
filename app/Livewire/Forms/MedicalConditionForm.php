<?php

namespace App\Livewire\Forms;

use App\Models\MedicalCondition;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MedicalConditionForm extends Form
{
    #[Validate('required|string|max:255')]
    public $condition_name;

    #[Validate('required')]
    public $diagnosis_date;

    #[Validate('required')]
    public $status;

    #[Validate('required')]
    public $treatment_plan;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $encounter_id;

    public function store()
    {
        $this->validate();

        MedicalCondition::create([
            'condition_name' => $this->condition_name,
            'diagnosis_date' => $this->diagnosis_date,
            'status' => $this->status,
            'treatment_plan' => $this->treatment_plan,
            'notes' => $this->notes,
            'encounter_id' => $this->encounter_id,
            'patient_id' => $this->patient_id,
        ]);

    }

    public function empty()
    {
        $this->condition_name = '';
        $this->diagnosis_date = '';
        $this->status = '';
        $this->treatment_plan = '';
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
