<?php

namespace App\Livewire\Forms;

use App\Models\Vital;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VitalForm extends Form
{
    public $blood_pressure;
    public $heart_rate;
    public $respiratory_rate;
    public $encounter_id;
    public $oxygen_saturation;
    public $blood_sugar;

    #[Validate('required')]
    public $patient_id;

    #[Validate('required')]
    public $temperature;
    public function store()
    {
        $this->validate();

        Vital::create([
            'patient_id' => $this->patient_id,
            'temperature' => $this->temperature,
            'blood_pressure' => $this->blood_pressure,
            'heart_rate' => $this->heart_rate,
            'respiratory_rate' => $this->respiratory_rate,
            'oxygen_saturation' => $this->oxygen_saturation,
            'blood_sugar' => $this->blood_sugar,
        ]);
    }

    public function empty()
    {
        $this->temperature = '';
        $this->blood_pressure = '';
        $this->heart_rate = '';
        $this->respiratory_rate = '';
        $this->oxygen_saturation = '';
        $this->blood_sugar = '';
    }

    public function setPatientId($patient_id)
    {
        $this->patient_id = $patient_id;
    }
}
