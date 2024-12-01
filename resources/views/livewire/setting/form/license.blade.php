<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount};

form(SettingForm::class);
state(['notification' => null]);

mount(function () {
    $settings = ['prc', 'ptr', 's2'];
    foreach ($settings as $setting) {
        $this->form->settings[$setting] = config("settings.{$setting}");
    }
});

$store = function () {
    try {
        $this->form->store();
        $this->dispatch('refresh');
        $this->notification = ['type' => 'success', 'message' => 'License settings updated successfully'];
    } catch (\Exception $e) {
        $this->notification = ['type' => 'error', 'message' => 'Failed to update settings'];
    }
};

?>

<section>
    <x-card>
        <x-slot name="header">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('License Information') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Update your account's license information.") }}
            </p>
        </x-slot>

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

        <form wire:submit="store">
            <div class="space-y-4 p-4">
                @foreach (['prc' => 'PRC License', 'ptr' => 'PTR License', 's2' => 'S2 License'] as $field => $label)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                        <x-input-label for="{{ $field }}" :value="__($label)"
                            class="text-sm font-medium text-gray-700 md:pt-2" />
                        <div class="md:col-span-3">
                            <x-text-input wire:model.live.blur="form.settings.{{ $field }}" id="{{ $field }}"
                                :placeholder="__($label . ' number')" class="w-full" type="text" />
                            <x-input-error :messages="$errors->get('form.settings.' . $field)" class="mt-1" />
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-1 px-4">
                <div class="text-right">
                    <x-primary-button wire:loading.attr="disabled" wire:target="store">
                        <span wire:loading.remove wire:target="store">{{ __('Save') }}</span>
                        <span wire:loading wire:target="store" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            {{ __('Saving...') }}
                        </span>
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-card>
</section>
