<?php

namespace App\Livewire\Forms;

use App\Models\Patient;
use Livewire\Attributes\Validate;
use App\Livewire\Forms\UserForm;

class PatientForm extends UserForm
{

    public $newPatient;

    #[Validate('required')]
    public $first_name;

    #[Validate('required')]
    public $last_name;

    #[Validate('required')]
    public $height;

    #[Validate('required')]
    public $weight;

    #[Validate('required')]
    public $phone_number;

    #[Validate('required')]
    public $gender;


    public function setPatient(?Patient $patient = null)
    {
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->height = $patient->height;
        $this->weight = $patient->weight;
        $this->phone_number = $patient->phone_number;
        $this->gender = $patient->gender;
    }

    public function store(?Patient $patient)
    {
        $this->validate();

        if ($patient) {
            $this->update($patient);
        } else {
            $this->newPatient = $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'height', 'weight', 'gender');
    }

    public function update(Patient $patient)
    {
        $patient->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => $this->gender
        ]);
    }

    public function create()
    {
        $patient = Patient::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => $this->gender,
            'user_id' => $this->ensureStoreUser()->id,
        ]);

        return $patient;
    }

    public function clearInputs()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone_number = '';
        $this->height = '';
        $this->weight = '';
        $this->gender = '';
    }
}
