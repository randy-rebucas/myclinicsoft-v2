<?php

namespace App\Livewire\Forms;

use App\Models\Patient;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PatientForm extends Form
{
    #[Validate('required|string|max:255')] 
    public $first_name;

    #[Validate('required|string|max:255')] 
    public $last_name;

    #[Validate('required')] 
    public $phone_number;

    #[Validate('required')] 
    public $date_of_birth;

    #[Validate('required')] 
    public $gender;

    #[Validate('required|string|max:255')] 
    public $name;

    #[Validate('required|string|lowercase|email|max:255|unique:' . User::class)] 
    public $email;

    #[Validate('required|string')] 
    public $password;

    public ?Patient $patient = null;

    public function store(?Patient $patient)
    {
        $this->validate();
        if ($patient) {
            $this->patient = $patient;
            $this->patient->where('id', $this->patient->id)->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender
            ]);
        } else {
            $user = $this->ensureStoreUser();
        
            Patient::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'user_id' => $user->id,
            ]);
        }
        
        // $this->reset(); 
    }

    public function ensureStoreUser()
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        return $user;
    }

    public function setPatient(?Patient $patient = null) {
        $this->patient = $patient;
        $this->first_name = $this->patient->first_name;
        $this->last_name = $this->patient->last_name;
        $this->phone_number = $this->patient->phone_number;
        $this->date_of_birth = $this->patient->date_of_birth;
        $this->gender = $this->patient->gender;
    }
}
