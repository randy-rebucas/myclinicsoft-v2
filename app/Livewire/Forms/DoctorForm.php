<?php

namespace App\Livewire\Forms;

use App\Models\Doctor;
use App\Livewire\Forms\UserForm;

class DoctorForm extends UserForm
{
    public function setDoctor(?Doctor $doctor = null)
    {
        $this->first_name = $doctor->first_name;
        $this->last_name = $doctor->last_name;
        $this->phone_number = $doctor->phone_number;
        $this->gender = $doctor->gender;
    }

    public function store(?Doctor $doctor)
    {
        $this->validate();

        if ($doctor) {
            $this->update($doctor);
        } else {
           $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'gender');
    }

    public function update(Doctor $doctor)
    {
        $doctor->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender
        ]);
    }

    public function create()
    {
        Doctor::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $this->ensureStoreUser()->id,
        ]);
    }
}
