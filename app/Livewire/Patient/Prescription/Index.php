<?php

namespace App\Livewire\Patient\Prescription;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Prescription;

class Index extends Component
{
    public Patient $patient;
    
    public function render()
    {
        $prescriptions = Prescription::where('patient_id', $this->patient->id)
            ->latest()
            ->get();

        return view('livewire.patient.prescription.index', [
            'prescriptions' => $prescriptions
        ]);
    }
} 