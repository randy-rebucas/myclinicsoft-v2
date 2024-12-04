<?php

namespace App\Livewire\Forms;

use App\Models\Clinic;
use App\Models\ClinicDoctor;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class ClinicForm extends Form
{
    public $clinic;
    public $description;
    public $is_active = true;

    #[Validate('required|string|max:255')]
    public $name;

    #[Validate('required|string|max:255')]
    public $address;

    #[Validate('required')]
    public $city;

    #[Validate('required|string|max:2')]
    public $state;

    #[Validate('required|numeric|digits:4')]
    public $zip;

    #[Validate('required')]
    public $phone;

    #[Validate('required|string|lowercase|email|max:255')]
    public $email;

    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Clinic::class)->ignore($this->clinic?->id),
            ],
        ];
    }
    // #[Validate('nullable|string|max:255')]
    // public $logo;

    public function setClinic(?Clinic $clinic = null)
    {
        $this->clinic = $clinic;
        $this->name = $clinic->name;
        $this->address = $clinic->address;
        $this->city = $clinic->city;
        $this->state = $clinic->state;
        $this->zip = $clinic->zip;
        $this->phone = $clinic->phone;
        $this->email = $clinic->email;
        $this->description = $clinic->description;
        $this->is_active = $clinic->is_active;
    }

    public function store()
    {
        $this->validate();

        if ($this->clinic) {
            $this->update($this->clinic);
        } else {
            $newClinic = $this->create();
            $doctor = Auth::user()->doctor;
            $newClinic->doctors()->attach($doctor->id, ['is_primary' => false]);
            $this->reset();
        }
    }

    public function update(Clinic $clinic)
    {
        $clinic->update([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
    }

    public function create()
    {
        $clinic = Clinic::create([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        return $clinic;
    }
}
