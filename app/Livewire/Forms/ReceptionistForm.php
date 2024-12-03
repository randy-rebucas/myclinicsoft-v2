<?php

namespace App\Livewire\Forms;

use App\Models\Receptionist;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\Auth;

class ReceptionistForm extends UserForm
{
    public $doctor_id;

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
            'doctor_id' => $this->doctor_id
        ]);
    }

    public function create()
    {
        Receptionist::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $this->ensureStoreUser()->id,
            'doctor_id' => $this->doctor_id
        ]);
    }
}
