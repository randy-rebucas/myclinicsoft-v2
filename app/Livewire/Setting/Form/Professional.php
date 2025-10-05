<?php

namespace App\Livewire\Setting\Form;

use Livewire\Component;
use App\Models\Doctor;

class Professional extends Component
{
    public $doctor;
    public $specialty = '';
    public $license_number = '';
    public $npi_number = '';
    public $consultation_fee = '';
    public $bio = '';
    public $available_hours = [];
    public $phone_number = '';
    public $meta = [];

    public function mount()
    {
        $this->doctor = auth()->user()->doctor;
        
        // Initialize form fields with current doctor data
        if ($this->doctor) {
            $this->specialty = $this->doctor->specialty ?? '';
            $this->license_number = $this->doctor->license_number ?? '';
            $this->npi_number = $this->doctor->npi_number ?? '';
            $this->consultation_fee = $this->doctor->consultation_fee ?? '';
            $this->bio = $this->doctor->bio ?? '';
            $this->phone_number = $this->doctor->phone_number ?? '';
            
            // Initialize available hours from available_hours column
            $this->available_hours = $this->doctor->available_hours ?? [
                ['day' => 'Monday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true],
                ['day' => 'Tuesday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true],
                ['day' => 'Wednesday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true],
                ['day' => 'Thursday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true],
                ['day' => 'Friday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true],
                ['day' => 'Saturday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => false],
                ['day' => 'Sunday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => false],
            ];
            
            // Initialize meta data
            $this->meta = $this->doctor->meta ?? [];
        }
    }

    public function save()
    {
        try {
            $this->doctor->update([
                'specialty' => $this->specialty,
                'license_number' => $this->license_number,
                'npi_number' => $this->npi_number,
                'consultation_fee' => $this->consultation_fee,
                'bio' => $this->bio,
                'phone_number' => $this->phone_number,
                'available_hours' => $this->available_hours,
                'meta' => $this->meta,
            ]);
            
            session()->flash('success', 'Professional information updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update professional information: ' . $e->getMessage());
        }
    }

    public function addTimeSlot()
    {
        $this->available_hours[] = [
            'day' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_available' => true
        ];
    }

    public function removeTimeSlot($index)
    {
        unset($this->available_hours[$index]);
        $this->available_hours = array_values($this->available_hours);
    }

    public function updateTimeSlot($index, $field, $value)
    {
        $this->available_hours[$index][$field] = $value;
    }

    public function addMetaField()
    {
        $this->meta[] = [
            'key' => '',
            'value' => ''
        ];
    }

    public function removeMetaField($index)
    {
        unset($this->meta[$index]);
        $this->meta = array_values($this->meta);
    }

    public function updateMetaField($index, $field, $value)
    {
        $this->meta[$index][$field] = $value;
    }

    public function render()
    {
        return view('livewire.setting.form.professional');
    }
}
