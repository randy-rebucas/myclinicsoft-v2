<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount};

form(SettingForm::class);
state(['notification' => null]);

mount(function () {
    $this->form->settings['prc'] = config('settings.prc');
    $this->form->settings['ptr'] = config('settings.ptr');
    $this->form->settings['s2'] = config('settings.s2');
});

$store = function () {
    try {
        $this->form->store();
        $this->dispatch('refresh');
        $this->notification = [
            'type' => 'success',
            'message' => 'License settings updated successfully'
        ];
    } catch (\Exception $e) {
        $this->notification = [
            'type' => 'error',
            'message' => 'Failed to update settings'
        ];
    }
};

?>

<div>
    <!-- Notification Banner -->
    @if ($notification)
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg {{ $notification['type'] === 'success' ? 'bg-green-500' : 'bg-red-500' }} text-white"
             role="alert">
            <div class="flex items-center">
                @if ($notification['type'] === 'success')
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
                <span class="font-medium">{{ $notification['message'] }}</span>
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <form wire:submit="store" class="space-y-6 p-6">
        <!-- PRC Field -->
        <div class="md:flex p-4 shadow rounded-lg hover:bg-gray-50 transition-colors">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="prc" :value="__('PRC')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input 
                    wire:model.blur="form.settings.prc" 
                    id="prc"
                    aria-label="{{ __('PRC') }}"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
                <x-input-error :messages="$errors->get('form.settings.prc')" class="mt-2" />
            </div>
        </div>

        <!-- PTR Field -->
        <div class="md:flex p-4 shadow rounded-lg hover:bg-gray-50 transition-colors">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="ptr" :value="__('PTR')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input 
                    wire:model.blur="form.settings.ptr" 
                    id="ptr"
                    aria-label="{{ __('PTR') }}"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
                <x-input-error :messages="$errors->get('form.settings.ptr')" class="mt-2" />
            </div>
        </div>

        <!-- S2 Field -->
        <div class="md:flex p-4 shadow rounded-lg hover:bg-gray-50 transition-colors">
            <div class="flex items-center md:w-1/4">
                <x-input-label for="s2" :value="__('S2')"
                    class="block text-gray-500 md:text-right mb-1 md:mb-0 pr-4" />
            </div>
            <div class="md:w-3/4">
                <x-text-input 
                    wire:model.blur="form.settings.s2" 
                    id="s2"
                    aria-label="{{ __('S2') }}"
                    class="w-full rounded py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500"
                    type="text" />
                <x-input-error :messages="$errors->get('form.settings.s2')" class="mt-2" />
            </div>
        </div>

        <!-- Save Button -->
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
