<?php

namespace App\Livewire\Forms;

use App\Models\MedRepresentative;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MedRepresentativeForm extends Form
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

    public ?MedRepresentative $medRepresentative = null;

    public function store(?MedRepresentative $medRepresentative)
    {
        $this->validate();

        if ($medRepresentative) {
            $medRepresentative->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender
            ]);
        } else {
            $user = $this->ensureStoreUser();

            MedRepresentative::create([
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

    public function setMedRepresentative(?MedRepresentative $medRepresentative = null)
    {
        $this->medRepresentative = $medRepresentative;
        $this->first_name = $medRepresentative->first_name;
        $this->last_name = $medRepresentative->last_name;
        $this->phone_number = $medRepresentative->phone_number;
        $this->gender = $medRepresentative->gender;
    }

    public function clearInputs()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->phone_number = '';
        $this->gender = '';
    }
}
