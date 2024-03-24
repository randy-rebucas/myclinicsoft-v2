<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
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

    public function ensureStoreUser()
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        return $user;
    }


}
