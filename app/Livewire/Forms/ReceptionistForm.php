<?php

namespace App\Livewire\Forms;

use App\Models\Receptionist;
use App\Mail\UserCredentialsMail;
use App\Models\User;
use App\Traits\GeneratesUserCredentials;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReceptionistForm extends Form
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

    public function setReceptionist(?Receptionist $receptionist = null)
    {
        $this->first_name = $receptionist->first_name;
        $this->last_name = $receptionist->last_name;
        $this->phone_number = $receptionist->phone_number;
        $this->gender = $receptionist->gender;
    }

    public function store(?Receptionist $receptionist)
    {
        $this->validate();

        if ($receptionist) {
            $this->update($receptionist);
        } else {
            $this->create();
        }

        $this->reset('first_name', 'last_name', 'phone_number', 'gender');
    }

    public function update(?Receptionist $receptionist)
    {
        $receptionist->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'doctor_id' => Auth::user()->doctor->id
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

        $receptionist = Receptionist::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $user->id,
            'doctor_id' => Auth::user()->doctor->id
        ]);

        // Mail::to($user->email)->send(new UserCredentialsMail($credentials));

        return $receptionist;
    }
}
