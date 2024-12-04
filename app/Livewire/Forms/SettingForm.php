<?php

namespace App\Livewire\Forms;

use App\Models\Setting;

use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;

class SettingForm extends Form
{
    // #[Validate([
    //     'settings.address' => ['required', 'string', 'max:1500'],
    // ], message: [
    //     'settings.address.required' => 'The :attribute are missing.',
    //     'settings.address.string' => 'The :attribute should be string.',
    //     'settings.address.max' => 'The :attribute is too long.',
    // ], attribute: [
    //     'settings.address' => 'business address',
    // ])]
    public $settings = [];

    public function mount()
    {
        $doctor = Auth::user()->doctor;
        foreach ($doctor->meta as $key => $value) {
            $this->settings[$key] = config("settings.{$key}");
        }
    }

    public function store()
    {
        foreach ($this->settings as $key => $value) {
            $doctor = Auth::user()->doctor;
            $metas = $doctor->meta;
            $metas[$key] = $value;
            $doctor->meta = $metas;
            $doctor->save();
        }
    }
}
