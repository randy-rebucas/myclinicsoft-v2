<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount, on, usesFileUploads};

usesFileUploads();

form(SettingForm::class);

mount(function () {
    $this->form->settings['logo'] = config('settings.logo');
    $this->form->settings['business_name'] = config('settings.business_name');
    $this->form->settings['business_contact'] = config('settings.business_contact');
    $this->form->settings['business_address'] = config('settings.business_address');
});

$store = function () {
    $this->form->store();

    $this->dispatch('refresh');
};

?>

<div>
    <form wire:submit="store" class="p-6">
        <div class="md:flex items-start mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="logo" :value="__('Logo')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="flex items-start md:w-3/4">
                <div class="w-48">
                    <img src="{{ asset('storage/' . config('settings.logo')) }}">
                </div>
                <input type="file" wire:model="form.settings.logo">
            </div>
        </div>
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_name" :value="__('Business Name')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model="form.settings.business_name" id="business_name"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
            </div>
        </div>
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_contact" :value="__('Contact')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model="form.settings.business_contact" id="business_contact"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
            </div>
        </div>
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="business_address" :value="__('Address')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-textarea wire:model="form.settings.business_address" id="business_address" name="business_address"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"></x-textarea>
                <x-input-error :messages="$errors->get('form.settings.business_address')" class="mt-2" />
            </div>
        </div>
        <div class="md:flex justify-end">
            <x-primary-button class="mb-2" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</div>
