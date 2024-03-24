<?php

namespace App\Livewire\Forms;

use App\Models\Receptionist;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReceptionistForm extends Form
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

    public ?Receptionist $receptionist = null;

    public function store(?Receptionist $receptionist)
    {
        $this->validate();

        if ($receptionist) {
            $receptionist->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender
            ]);
        } else {
            $user = $this->ensureStoreUser();

            Receptionist::create([
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

    public function setReceptionist(?Receptionist $receptionist = null)
    {
        $this->receptionist = $receptionist;
        $this->first_name = $receptionist->first_name;
        $this->last_name = $receptionist->last_name;
        $this->phone_number = $receptionist->phone_number;
        $this->gender = $receptionist->gender;
    }

    public function clearInputs()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone_number = '';
        $this->gender = '';
    }
}
