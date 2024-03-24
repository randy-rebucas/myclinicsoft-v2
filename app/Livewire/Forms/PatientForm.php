<?php

namespace App\Livewire\Forms;

use App\Models\Patient;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\UserForm;

class PatientForm extends UserForm
{

    #[Validate('required')]
    public $date_of_birth;

    public function setPatient(?Patient $patient = null)
    {
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->phone_number = $patient->phone_number;
        $this->date_of_birth = $patient->date_of_birth;
        $this->gender = $patient->gender;
    }

    public function store(?Patient $patient)
    {
        $this->validate();

        if ($patient) {
            $this->update($patient);
        } else {
            $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'date_of_birth', 'gender');
    }

    public function update(Patient $patient)
    {
        $patient->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender
        ]);
    }

    public function create()
    {
        Patient::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'user_id' => $this->ensureStoreUser()->id,
        ]);
    }
}
