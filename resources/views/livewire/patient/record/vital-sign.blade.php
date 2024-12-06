<?php

use App\Models\Vital;
use Carbon\Carbon;
use App\Livewire\Forms\VitalForm;
use function Livewire\Volt\{state, form, on, mount, computed};

state('patient');

form(VitalForm::class);

on([
    'set-encounter' => function ($encounterId) {
        // $this->form->setEncounterId($encounterId);
    },
]);

mount(function () {
    $vital = Vital::where('patient_id', $this->patient->id)
        ->whereDate('created_at', now()->toDateString())
        ->latest()
        ->first();
    $this->form->setPatientId($this->patient->id);
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'vital-signs']);
};

$delete = function (Vital $vital) {
    $vital->delete();

    $this->dispatch('refresh');
};

?>

<div>

    <form wire:submit="create">

        <!-- Blood Pressure -->
        <div class="mb-4">
            <x-input-label for="systolic" :value="__('Blood Pressure (mmHg)')" class="block text-sm font-medium text-gray-700" />
            <div class="flex gap-2">
                <div class="w-1/2">
                    <x-text-input wire:model.live="form.systolic" type="number" placeholder="Systolic" class="w-full" />
                    @error('form.systolic')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="w-1/2">
                    <x-text-input wire:model.live="form.diastolic" type="number" placeholder="Diastolic"
                        class="w-full" />
                    @error('form.diastolic')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Pulse Rate -->
        <div class="mb-4">
            <x-input-label for="heart_rate" :value="__('Pulse Rate (bpm)')" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.heart_rate" type="number"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            @error('form.heart_rate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Temperature -->
        <div class="mb-4">
            <x-input-label for="temperature" :value="__('Temperature (°C)')" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.temperature" type="number" step="0.1"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            @error('form.temperature')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Respiratory Rate -->
        <div class="mb-4">
            <x-input-label for="respiratory_rate" :value="__('Respiratory Rate (bpm)')" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.respiratory_rate" type="number"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            @error('form.respiratory_rate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Oxygen Saturation -->
        <div class="mb-4">
            <x-input-label for="oxygen_saturation" :value="__('Oxygen Saturation (%)')" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.oxygen_saturation" type="number"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            @error('form.oxygen_saturation')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Blood Sugar -->
        <div class="mb-4">
            <x-input-label for="blood_sugar" :value="__('Blood Sugar (mg/dL)')" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.blood_sugar" type="number"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            @error('form.blood_sugar')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            {{ __('Save') }}
        </button>
    </form>

</div>
