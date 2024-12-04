<?php

use App\Livewire\Forms\ClinicForm;
use App\Models\ClinicDoctor;
use function Livewire\Volt\{state, form, mount, usesFileUploads, on};

usesFileUploads();

form(ClinicForm::class);

state([
    'notification' => null,
    'logo' => null,
    'clinic' => null,
    'clinics' => [],
    'showModal' => false,
]);

mount(function () {
    $this->clinics = auth()->user()->doctor->clinics()->get();
});

on(['clinic-saved' => function() {
    $this->clinics = auth()->user()->doctor->clinics()->get();
    $this->showModal = false;
}]);

$editClinic = function ($clinicId) {
    $this->clinic = $this->clinics->find($clinicId);
    $this->form->setClinic($this->clinic);
    $this->showModal = true;
};

$deleteClinic = function ($clinicId) {
    $doctor = Auth::user()->doctor;
    $doctor->clinics()->detach($clinicId);

    $clinic = $this->clinics->find($clinicId);
    $clinic->delete();

    $this->dispatch('clinic-saved');
    $this->notification = ['type' => 'success', 'message' => 'Clinic deleted successfully'];
};

$save = function () {
    try {
        $this->form->store();
        $this->showModal = false;
        $this->dispatch('clinic-saved');
        $this->notification = ['type' => 'success', 'message' => 'Clinic details updated successfully'];
    } catch (\Exception $e) {
        $this->notification = ['type' => 'error', 'message' => 'Failed to update clinic details'];
    }
};

$newClinic = function () {
    $this->clinic = null;
    $this->form->reset();
    $this->showModal = true;
};

?>

<section>
    <x-card>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Manage Clinics') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Manage your clinic locations.') }}
                    </p>
                </div>
                <button type="button" wire:click="newClinic" class="btn-primary inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('New Clinic') }}
                </button>
            </div>
        </x-slot>

        <div class="divide-y divide-gray-200">
            @foreach ($clinics as $clinicItem)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            @if($clinicItem->is_active)
                                <svg class="w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $clinicItem->name }}</div>
                                <div class="text-sm text-gray-500">{{ $clinicItem->address }}, {{ $clinicItem->city }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button wire:click="editClinic({{ $clinicItem->id }})" class="btn-secondary p-2">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                        <button
                            x-data
                            @click="if (confirm('Are you sure you want to delete this clinic?')) { $wire.deleteClinic({{ $clinicItem->id }}) }"
                            class="btn-danger p-2">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

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

        <div x-data="{ show: @entangle('showModal') }">
            <!-- Modal -->
            <div x-show="show" x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                class="fixed bottom-0 left-1/2 -translate-x-1/2 z-50 bg-white rounded-t-xl shadow-lg"
                style="height: 80vh; width: 70%;">

                <!-- Modal Header -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $clinic ? __('Edit Clinic') : __('New Clinic') }}
                        </h3>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body with Form -->
                <div class="overflow-y-auto h-full">
                    <form wire:submit="save" class="bg-white">
                        <div class="space-y-4 p-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="name" :value="__('Name')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.name" id="name" :placeholder="__('Enter Name')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="address" :value="__('Address')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.address" id="address" :placeholder="__('Enter Address')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.address')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="city" :value="__('City')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.city" id="city" :placeholder="__('Enter City')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.city')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="state" :value="__('State')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.state" id="state" :placeholder="__('Enter State')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.state')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="zip" :value="__('Zip')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.zip" id="zip" :placeholder="__('Enter Zip')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.zip')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="phone" :value="__('Phone')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.phone" id="phone" :placeholder="__('Enter Phone')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.phone')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="email" :value="__('Email')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-text-input wire:model.live.blur="form.email" id="email" :placeholder="__('Enter Email')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="description" :value="__('Description')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-textarea wire:model.live.blur="form.description" id="description"
                                        :placeholder="__('Enter Description')" class="w-full" rows="3" />
                                    <x-input-error :messages="$errors->get('form.description')" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                                <x-input-label for="is_active" :value="__('Is Active')"
                                    class="text-sm font-medium text-gray-700 md:pt-2" />

                                <div class="md:col-span-3">
                                    <x-toggle wire:model.live.blur="form.is_active" id="is_active" :placeholder="__('Is Active')"
                                        class="w-full" />
                                    <x-input-error :messages="$errors->get('form.is_active')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 px-4 pb-4">
                            <div class="text-right">
                                <x-primary-button wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">{{ $clinic ? __('Update Clinic') : __('Create Clinic') }}</span>
                                    <span wire:loading wire:target="save" class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        {{ __('Saving...') }}
                                    </span>
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Backdrop -->
            <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="show = false"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40">
            </div>
        </div>
    </x-card>
</section>
