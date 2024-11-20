<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount, on, usesFileUploads};

usesFileUploads();

form(SettingForm::class);

state(['logoPreview' => null]);

mount(function () {
    $this->form->settings['logo'] = config('settings.logo');
    $this->form->settings['business_name'] = config('settings.business_name');
    $this->form->settings['business_owner'] = config('settings.business_owner');
    $this->form->settings['business_contact'] = config('settings.business_contact');
    $this->form->settings['business_address'] = config('settings.business_address');
});

$updatedFormSettingsLogo = function () {
    if ($this->form->settings['logo']) {
        $this->logoPreview = $this->form->settings['logo']->temporaryUrl();
    }
};

$store = function () {
    try {
        $this->form->store();
        $this->dispatch('refresh');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Business settings updated successfully'
        ]);
    } catch (\Exception $e) {
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'Failed to update settings'
        ]);
    }
};

?>

<div>
    <form wire:submit="store" class="space-y-6 p-6">
        <div class="md:flex items-start p-4 shadow rounded-lg" 
             x-data="{ uploading: false }" 
             x-on:livewire-upload-start="uploading = true" 
             x-on:livewire-upload-finish="uploading = false">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="logo" :value="__('Logo')" class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="flex flex-col space-y-4 md:w-3/4">
                <div class="w-48">
                    @if ($logoPreview)
                        <img src="{{ $logoPreview }}" class="rounded-lg">
                    @elseif (config('settings.logo'))
                        <img src="{{ asset('storage/' . config('settings.logo')) }}" class="rounded-lg">
                    @else
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <input type="file" 
                           wire:model="form.settings.logo" 
                           accept="image/*"
                           aria-label="{{ __('Choose logo file') }}"
                           class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700
                                hover:file:bg-purple-100" />
                    
                    <div x-show="uploading">
                        <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('form.settings.logo')" class="mt-2" />
                <p class="text-sm text-gray-500">Recommended size: 200x200px. Max size: 1MB</p>
            </div>
        </div>

        <div class="md:flex p-4 shadow rounded-lg hover:bg-gray-50 transition-colors">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_name" :value="__('Business Name')" class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input 
                    wire:model.blur="form.settings.business_name" 
                    id="business_name"
                    aria-label="{{ __('Business Name') }}"
                    class="w-full" 
                    type="text" 
                    required />
                <x-input-error :messages="$errors->get('form.settings.business_name')" class="mt-2" />
            </div>
        </div>

        <div class="md:flex p-4 shadow rounded-lg">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_owner" :value="__('Business Owner')" class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model.blur="form.settings.business_owner" id="business_owner"
                    class="w-full" type="text" />
                <x-input-error :messages="$errors->get('form.settings.business_owner')" class="mt-2" />
            </div>
        </div>

        <div class="md:flex p-4 shadow rounded-lg">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_contact" :value="__('Contact')" class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model.blur="form.settings.business_contact" id="business_contact"
                    class="w-full" type="text" />
                <x-input-error :messages="$errors->get('form.settings.business_contact')" class="mt-2" />
            </div>
        </div>

        <div class="md:flex p-4 shadow rounded-lg">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_address" :value="__('Address')" class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-textarea wire:model.blur="form.settings.business_address" id="business_address" 
                    class="w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.settings.business_address')" class="mt-2" />
            </div>
        </div>

        <div class="md:flex justify-end pt-4">
            <x-primary-button 
                class="mb-2" 
                wire:loading.attr="disabled"
                wire:target="store">
                <span wire:loading.remove wire:target="store">{{ __('Save') }}</span>
                <span wire:loading wire:target="store" class="inline-flex items-center">
                    <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
