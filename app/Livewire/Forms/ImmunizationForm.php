<?php

namespace App\Livewire\Forms;

use App\Models\Immunization;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ImmunizationForm extends Form
{
    #[Validate('required|string|max:255')]
    public $vaccine_name;

    #[Validate('required|date')]
    public $date_administered;

    #[Validate('required')]
    public $administrator;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    public function store()
    {
        $this->validate();

        Immunization::create([
            'vaccine_name' => $this->vaccine_name,
            'date_administered' => $this->date_administered,
            'administrator' => $this->administrator,
            'notes' => $this->notes,
            'patient_id' => $this->patient_id,
        ]);

    }

    public function empty()
    {
        $this->vaccine_name = '';
        $this->date_administered = '';
        $this->administrator = '';
        $this->notes = '';
    }
}
