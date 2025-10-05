<?php

namespace App\Livewire\Forms;

use App\Models\Doctor;
use Livewire\Form;
use App\Traits\GeneratesUserCredentials;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\GenderEnum;

class DoctorForm extends Form
{
    use GeneratesUserCredentials;

    #[Validate('required|string|max:255')]
    public $first_name;

    #[Validate('required|string|max:255')]
    public $last_name;

    #[Validate('required')]
    public $phone_number;

    #[Validate('required|in:male,female,unknown')]
    public $gender;

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
        $credentials = $this->generateCredentials($this->first_name, $this->last_name);

        $user = User::create([
            'name' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make('password'),
        ]);

        $doctor = Doctor::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $user->id,
        ]);

        // Log doctor creation activity (aligns with ActivitySeeder)
        $doctor->recordActivity('created', 'Doctor profile was created');
        
        // Log role assignment activity
        $doctor->recordActivity('assigned role doctor', 'Doctor role was assigned');
    }
}
