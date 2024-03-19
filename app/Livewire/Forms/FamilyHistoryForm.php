<?php

namespace App\Livewire\Forms;

use App\Models\FamilyHistory;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FamilyHistoryForm extends Form
{
    #[Validate('required|string|max:255')]
    public $relationship;

    #[Validate('required|string|max:255')]
    public $condition;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    public function store()
    {
        $this->validate();

        FamilyHistory::create([
            'relationship' => $this->relationship,
            'condition' => $this->condition,
            'notes' => $this->notes,
            'patient_id' => $this->patient_id,
        ]);

    }

    public function empty()
    {
        $this->relationship = '';
        $this->condition = '';
        $this->notes = '';
    }
}
