<?php

namespace App\Livewire\Forms;

use App\Models\Setting;

use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;

class SettingForm extends Form
{
    #[Validate([
        'settings.business_address' => ['required', 'string', 'max:255'],
    ], message: [
        'settings.business_address.required' => 'The :attribute are missing.',
        'settings.business_address.string' => 'The :attribute should be string.',
        'settings.business_address.max' => 'The :attribute is too long.',
    ], attribute: [
        'settings.business_address' => 'business address',
    ])]
    public $settings = [];

    public function store()
    {
        $this->validate();

        foreach ($this->settings as $key => $value) {
            if ($key == 'logo') {
                if (!is_string($this->settings['logo'])) {
                    $name = $this->settings['logo']->getClientOriginalName();
                    $path = $this->settings['logo']->storeAs('images', $name, 'public');
                    $value = $path;
                }
            }
            $item = Setting::where('key', $key)->first();

            if (empty ($item)) {
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                ]);
            } else {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        }
    }
}
