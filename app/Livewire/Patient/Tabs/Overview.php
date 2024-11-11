<?php

namespace App\Livewire\Patient\Tabs;

use Livewire\Component;
use App\Models\Patient;

class Overview extends Component
{
    public Patient $patient;

    public function render()
    {
        return view('livewire.patient.tabs.overview');
    }
} 