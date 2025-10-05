<?php

namespace App\Livewire\Setting\Form;

use Livewire\Component;
use App\Models\Doctor;

class ClinicAssociations extends Component
{
    public $clinics = [];
    public $primaryClinic = null;

    public function mount()
    {
        $this->clinics = auth()->user()->doctor->clinics()->get();
        $this->primaryClinic = auth()->user()->doctor->clinics()->wherePivot('is_primary', true)->first();
    }

    public function setPrimary($clinicId)
    {
        $doctor = auth()->user()->doctor;
        
        // Remove primary status from all clinics
        $doctor->clinics()->updateExistingPivot($doctor->clinics->pluck('id'), ['is_primary' => false]);
        
        // Set new primary clinic
        $doctor->clinics()->updateExistingPivot($clinicId, ['is_primary' => true]);
        
        // Refresh the data
        $this->clinics = $doctor->clinics()->get();
        $this->primaryClinic = $doctor->clinics()->wherePivot('is_primary', true)->first();
        
        session()->flash('success', 'Primary clinic updated successfully!');
    }

    public function render()
    {
        return view('livewire.setting.form.clinic-associations');
    }
}
