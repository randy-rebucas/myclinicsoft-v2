<?php

namespace App\Livewire\Forms;

use App\Models\Encounter;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EncounterForm extends Form
{
    #[Validate('required|string|max:255')]
    public $chief_complaint;

    #[Validate('required')]
    public $encounter_date;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    public function store()
    {
        $this->validate();

        $encounter = Encounter::create([
            'chief_complaint' => $this->chief_complaint,
            'encounter_date' => $this->encounter_date,
            'notes' => $this->notes,
            'patient_id' => $this->patient_id,
        ]);

        // Log encounter creation activity (aligns with ActivitySeeder)
        $encounter->recordActivity('created', 'Medical encounter was created');

        return $encounter->id;
    }

    public function empty()
    {
        $this->chief_complaint = '';
        $this->encounter_date = '';
        $this->notes = '';
    }
}
