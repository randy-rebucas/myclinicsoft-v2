<?php

use App\Livewire\Forms\SettingForm;
use function Livewire\Volt\{state, form, mount};

form(SettingForm::class);
state(['notification' => null, 'logo' => null]);

mount(function () {
    $this->form->settings['business_name'] = config('settings.business_name');
    $this->form->settings['address'] = config('settings.address');
    $this->form->settings['contact_number'] = config('settings.contact_number');
    $this->form->settings['email'] = config('settings.email');
});

$store = function () {
    try {
        $this->form->store();
        $this->dispatch('refresh');
        $this->notification = [
            'type' => 'success',
            'message' => 'Business details updated successfully'
        ];
    } catch (\Exception $e) {
        $this->notification = [
            'type' => 'error',
            'message' => 'Failed to update business details'
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
        <!-- Logo Section -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                <x-input-label
                    for="logo"
                    :value="__('Business Logo')"
                    class="font-medium text-gray-700 md:text-right" />

                <div class="md:col-span-3 space-y-4">
                    <div class="flex items-center gap-4">
                        <!-- Logo Preview -->
                        <div class="relative w-24 h-24 rounded-lg bg-gray-50 border border-gray-200 overflow-hidden">
                            @if ($form->settings['logo'] ?? false)
                                <img src="{{ Storage::url($form->settings['logo']) }}"
                                     alt="Business Logo"
                                     class="w-full h-full object-cover">
                                <!-- Remove Logo Button -->
                                <button type="button"
                                        wire:click="removeLogo"
                                        class="absolute top-1 right-1 p-1 bg-red-500 rounded-full text-white hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @else
                                <div class="flex items-center justify-center w-full h-full text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Button -->
                        <div class="flex-1">
                            <input type="file"
                                   wire:model="logo"
                                   id="logo"
                                   class="hidden"
                                   accept="image/*" />
                            <label for="logo"
                                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                                {{ __('Upload New Logo') }}
                            </label>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __('Recommended size: 200x200px. Max file size: 1MB') }}
                            </p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Business Details Fields -->
        @foreach([
            'business_name' => 'Business Name',
            'address' => 'Business Address',
            'contact_number' => 'Contact Number',
            'email' => 'Email Address'
        ] as $field => $label)
            <div class="p-6 transition duration-150 hover:bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                    <x-input-label
                        for="{{ $field }}"
                        :value="__($label)"
                        class="font-medium text-gray-700 md:text-right" />

                    <div class="md:col-span-3">
                        @if ($field === 'address')
                            <x-textarea
                                wire:model.blur="form.settings.{{ $field }}"
                                id="{{ $field }}"
                                :placeholder="__('Enter complete business address')"
                                class="w-full rounded-lg"
                                rows="3" />
                        @else
                            <x-text-input
                                wire:model.blur="form.settings.{{ $field }}"
                                id="{{ $field }}"
                                :placeholder="__('Enter ' . strtolower($label))"
                                class="w-full rounded-lg"
                                type="{{ $field === 'email' ? 'email' : 'text' }}" />
                        @endif
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
