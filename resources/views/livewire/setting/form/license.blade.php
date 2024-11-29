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

<div class="max-w-4xl mx-auto">
    <!-- Notification Banner -->
    @if ($notification)
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 z-50 rounded-lg shadow-lg {{ $notification['type'] === 'success' ? 'bg-emerald-500' : 'bg-red-500' }} text-white"
             role="alert">
            <div class="flex items-center p-3">
                @if ($notification['type'] === 'success')
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
                <span class="font-medium text-sm">{{ $notification['message'] }}</span>
            </div>
        </div>
    @endif

    <!-- Main Form -->
    <form wire:submit="store" class="bg-white rounded-xl shadow-md divide-y divide-gray-100">
        <!-- License Fields -->
        @foreach(['prc' => 'PRC License', 'ptr' => 'PTR License', 's2' => 'S2 License'] as $field => $label)
            <div class="p-6 transition duration-150 hover:bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                    <x-input-label
                        for="{{ $field }}"
                        :value="__($label)"
                        class="font-medium text-gray-700 md:text-right" />

                    <div class="md:col-span-3">
                        <x-text-input
                            wire:model.blur="form.settings.{{ $field }}"
                            id="{{ $field }}"
                            :placeholder="__('Enter ' . $label . ' number')"
                            class="w-full rounded-lg"
                            type="text" />
                        <x-input-error :messages="$errors->get('form.settings.' . $field)" class="mt-2" />
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
            <x-primary-button
                wire:loading.attr="disabled"
                wire:target="store">
                <span wire:loading.remove wire:target="store">
                    {{ __('Save Changes') }}
                </span>
                <span wire:loading wire:target="store" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
