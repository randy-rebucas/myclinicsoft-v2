<?php

namespace App\Livewire\Forms;

use App\Models\DiagnosticTest;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DiagnosticTestForm extends Form
{
    #[Validate('required|string|max:255')]
    public $test_name;
    
    #[Validate('required|date')]
    public $test_date;

    #[Validate('required|string|max:255')]
    public $results;

    #[Validate('max:3000')]
    public $notes;

    #[Validate('required')]
    public $patient_id;

    public function store()
    {
        $validated = $this->validate();

        DiagnosticTest::create($validated);
    }

    public function empty()
    {
        $this->test_name = '';
        $this->test_date = '';
        $this->results = '';
        $this->patient_id = '';
        $this->notes = '';
    }
}
