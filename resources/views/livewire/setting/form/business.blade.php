<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount};

form(SettingForm::class);
state(['notification' => null, 'logo' => null]);

mount(function () {
    $settings = ['business_name', 'address', 'contact_number', 'email'];
    foreach ($settings as $setting) {
        $this->form->settings[$setting] = config("settings.{$setting}");
    }
});

$store = function () {
    try {
        $this->form->store();
        $this->dispatch('refresh');
        $this->notification = ['type' => 'success', 'message' => 'Business details updated successfully'];
    } catch (\Exception $e) {
        $this->notification = ['type' => 'error', 'message' => 'Failed to update business details'];
    }
};

?>

<div class="max-w-4xl mx-auto">
    @if ($notification)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 z-50 rounded-lg shadow-lg {{ $notification['type'] === 'success' ? 'bg-emerald-500' : 'bg-red-500' }} text-white p-3">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="{{ $notification['type'] === 'success' ? 'M4.5 12.75l6 6 9-13.5' : 'M6 18L18 6M6 6l12 12' }}" />
                </svg>
                <span class="font-medium text-sm">{{ $notification['message'] }}</span>
            </div>
        </div>
    @endif

    <form wire:submit="store" class="bg-white">
        <div class="space-y-4 p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <div class="md:col-span-3">
                    <div class="flex items-center gap-4">
                        <div
                            class="align-middle bg-gray-50 border border-gray-200 flex h-24 overflow-hidden relative rounded-lg w-24">
                            @if ($form->settings['logo'] ?? false)
                                <img src="{{ Storage::url($form->settings['logo']) }}" alt="Logo"
                                    class="w-full h-full object-cover">
                                <button type="button" wire:click="removeLogo"
                                    class="absolute top-1 right-1 p-1 bg-red-500 rounded-full text-white hover:bg-red-600">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @else
                                <svg class="w-8 h-8 m-auto text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <input type="file" wire:model.live="logo" id="logo" class="hidden" accept="image/*" />
                            <label for="logo" class="btn-secondary">{{ __('Upload New Logo') }}</label>
                            <p class="mt-2 text-sm text-gray-500">{{ __('Recommended: 200x200px. Max: 1MB') }}</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                </div>
            </div>


            @foreach (['business_name' => 'Business Name', 'address' => 'Business Address', 'contact_number' => 'Contact Number', 'email' => 'Email Address'] as $field => $label)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="{{ $field }}" :value="__($label)"
                        class="text-sm font-medium text-gray-700 md:pt-2" />

                    <div class="md:col-span-3">
                        @if ($field === 'address')
                            <x-textarea wire:model.live.blur="form.settings.{{ $field }}" id="{{ $field }}"
                                :placeholder="__('Enter ' . strtolower($label))" class="w-full" rows="3" />
                        @else
                            <x-text-input wire:model.live.blur="form.settings.{{ $field }}" id="{{ $field }}"
                                :placeholder="__('Enter ' . strtolower($label))" class="w-full" :type="$field === 'email' ? 'email' : 'text'" />
                        @endif
                        <x-input-error :messages="$errors->get('form.settings.' . $field)" class="mt-2" />
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
            <x-primary-button wire:loading.attr="disabled" wire:target="store">
                <span wire:loading.remove wire:target="store">{{ __('Save Changes') }}</span>
                <span wire:loading wire:target="store" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
