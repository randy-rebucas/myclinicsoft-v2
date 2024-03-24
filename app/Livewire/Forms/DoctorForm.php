<?php

namespace App\Livewire\Forms;

use App\Models\Doctor;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DoctorForm extends Form
{
    #[Validate('required|string|max:255')]
    public $first_name;

    #[Validate('required|string|max:255')]
    public $last_name;

    #[Validate('required')]
    public $phone_number;

    #[Validate('required')]
    public $gender;

    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('required|string|lowercase|email|max:255|unique:' . User::class)]
    public $email;

    #[Validate('required|string')]
    public $password;

    public ?Doctor $doctor = null;

    public function store(?Doctor $doctor)
    {
        $this->validate();

        if ($doctor) {
            $doctor->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender
            ]);
        } else {
            $user = $this->ensureStoreUser();

            Doctor::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender,
                'user_id' => $user->id,
            ]);
        }
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

    public function setDoctor(?Doctor $doctor = null)
    {
        $this->doctor = $doctor;
        $this->first_name = $doctor->first_name;
        $this->last_name = $doctor->last_name;
        $this->phone_number = $doctor->phone_number;
        $this->gender = $doctor->gender;
    }

    public function clearInputs()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone_number = '';
        $this->gender = '';
    }
}
