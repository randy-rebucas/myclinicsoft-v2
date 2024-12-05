<?php

namespace App\Livewire\Forms;

use App\Models\MedRepresentative;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use App\Traits\GeneratesUserCredentials;
use App\Models\User;
use Livewire\Form;

class MedRepresentativeForm extends Form
{
    use GeneratesUserCredentials;

    #[Validate('required|string|max:255')]
    public $first_name;

    #[Validate('required|string|max:255')]
    public $last_name;

    #[Validate('required')]
    public $phone_number;

    #[Validate('required')]
    public $gender;

    public function setMedRepresentative(?MedRepresentative $medRepresentative = null)
    {
        $this->first_name = $medRepresentative->first_name;
        $this->last_name = $medRepresentative->last_name;
        $this->phone_number = $medRepresentative->phone_number;
        $this->gender = $medRepresentative->gender;
    }

    public function store(?MedRepresentative $medRepresentative)
    {
        $this->validate();

        if ($medRepresentative) {
            $this->update($medRepresentative);
        } else {
            $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'gender');
    }

    public function update(?MedRepresentative $medRepresentative)
    {
        $medRepresentative->update([
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

        $medRepresentative = MedRepresentative::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $user->id,
        ]);

        $doctor = Auth::user()->doctor;
        $medRepresentative->doctors()->attach($doctor->id, ['is_active' => true]);
    }
}
