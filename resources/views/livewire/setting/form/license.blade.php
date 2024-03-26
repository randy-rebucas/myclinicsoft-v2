<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount};

form(SettingForm::class);

mount(function () {
    $this->form->settings['prc'] = config('settings.prc');
    $this->form->settings['ptr'] = config('settings.ptr');
    $this->form->settings['s2'] = config('settings.s2');
});

$store = function () {
    $this->form->store();
    $this->dispatch('refresh');
};

?>

<div>
    <form wire:submit="store" class="p-6">
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="prc" :value="__('PRC')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model="form.settings.prc" id="prc"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
            </div>
        </div>
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="ptr" :value="__('PTR')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model="form.settings.ptr" id="ptr"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
            </div>
        </div>
        <div class="md:flex mb-2 p-4 shadow">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="s2" :value="__('S2')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input wire:model="form.settings.s2" id="s2"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
                
            </div>
        </div>
        <div class="md:flex justify-end">
            <x-primary-button class="mb-2" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</div>
