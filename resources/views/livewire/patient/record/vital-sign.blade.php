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

$vitals = computed(function () {
    return Vital::query()
        ->where('patient_id', $this->patient->id)
        ->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-vital-sign');

    $this->dispatch('refresh');
};

$delete = function (Vital $vital) {
    $vital->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <x-table for="vital-sign">
        <x-table.thead>
            <x-table.row class="">
                <x-table.thead-cell :title="__('Systolic/Diastolic (mmHg)')" class="text-left" />
                <x-table.thead-cell :title="__('Pulse Rate (bpm)')" class="text-left" />
                <x-table.thead-cell :title="__('Temperature (°C)')" class="text-left" />
                <x-table.thead-cell :title="__('Respiratory Rate (bpm)')" class="text-left" />
                <x-table.thead-cell :title="__('Oxygen Saturation (%)')" class="text-left" />
                <x-table.thead-cell :title="__('Blood Sugar (mg/dL)')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click="$dispatch('open-modal', 'create-new-vital-sign')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="">
            @forelse ($this->vitals as $vital)
                <x-table.row class="bg-white " wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$vital->blood_pressure" />
                    <x-table.tbody-cell :item="$vital->heart_rate" />
                    <x-table.tbody-cell :item="$vital->temperature" />
                    <x-table.tbody-cell :item="$vital->respiratory_rate" />
                    <x-table.tbody-cell :item="$vital->oxygen_saturation" />
                    <x-table.tbody-cell :item="$vital->blood_sugar" />
                    <x-table.tbody-cell :item="$vital->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $vital->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </x-table.tbody-cell>
                </x-table.row>
            @empty
                <x-table.row class="bg-white  text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No vital sign record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-vital-sign" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Blood Pressure -->
                <div>
                    <x-input-label for="systolic" :value="__('Blood Pressure (mmHg)')" />
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <x-text-input wire:model.live="form.systolic" type="number" placeholder="Systolic"
                                class="w-full" />
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
                <div>
                    <x-input-label for="heart_rate" :value="__('Pulse Rate (bpm)')" />
                    <x-text-input wire:model.live="form.heart_rate" type="number" class="w-full" />
                    @error('form.heart_rate')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Temperature -->
                <div>
                    <x-input-label for="temperature" :value="__('Temperature (°C)')" />
                    <x-text-input wire:model.live="form.temperature" type="number" step="0.1" class="w-full" />
                    @error('form.temperature')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Respiratory Rate -->
                <div>
                    <x-input-label for="respiratory_rate" :value="__('Respiratory Rate (bpm)')" />
                    <x-text-input wire:model.live="form.respiratory_rate" type="number" class="w-full" />
                    @error('form.respiratory_rate')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Oxygen Saturation -->
                <div>
                    <x-input-label for="oxygen_saturation" :value="__('Oxygen Saturation (%)')" />
                    <x-text-input wire:model.live="form.oxygen_saturation" type="number" class="w-full" />
                    @error('form.oxygen_saturation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Blood Sugar -->
                <div>
                    <x-input-label for="blood_sugar" :value="__('Blood Sugar (mg/dL)')" />
                    <x-text-input wire:model.live="form.blood_sugar" type="number" class="w-full" />
                    @error('form.blood_sugar')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
