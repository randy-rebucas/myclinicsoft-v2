<?php

namespace App\Livewire\Forms;

use App\Models\MedRepresentative;
use App\Livewire\Forms\UserForm;

class MedRepresentativeForm extends UserForm
{
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
        MedRepresentative::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'user_id' => $this->ensureStoreUser()->id,
        ]);
    }
}
