<?php

use App\Models\Encounter;
use Carbon\Carbon;
use App\Livewire\Forms\EncounterForm;
use function Livewire\Volt\{state, form, mount, computed};

state(['patient', 'encounterId', 'filter', 'show' => false]);

form(EncounterForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$encounter = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->where('encounter_date', $this->filter ? $this->filter : Carbon::today())
        ->get()
        ->first();
});

$encounters = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->orderBy('encounter_date', 'desc')
        ->get();
});

$create = function () {
    $this->encounterId = $this->form->store();

    $this->form->empty();

    $this->dispatch('set-encounter', encounterId: $this->encounterId);

    $this->dispatch('close-modal', 'create-new-encounter');

    $this->dispatch('refresh');
};

$toggle = function () {
    $this->show = !$this->show;
};

$filterDate = function (Encounter $encounter) {
    $this->filter = $encounter->encounter_date;
    $this->show = false;
    $this->dispatch('set-encounter', encounterId: $encounter->id);
};
?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md" wire:loading.class="opacity-50">
        <legend class="dark:text-gray-200 px-2">{{ __('Latest Encounter') }}</legend>

        <div class="flex gap-4 items-end">
            <x-secondary-button class="ms-3 py-3" x-data=""
                x-on:click="$dispatch('open-modal', 'create-new-encounter')">
                {{ __('Create Encounter') }}
            </x-secondary-button>
            <x-secondary-button class="ms-3 py-3" wire:click="toggle">
                {{ __('View all Encounter') }}
            </x-secondary-button>
        </div>

        @if ($this->show)
            <x-table for="encounters">
                <x-table.thead>
                    <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                        <x-table.thead-cell :title="__('Chief Complaint')" class="text-left" />
                        <x-table.thead-cell :title="__('Date')" class="text-left" />
                    </x-table.row>
                </x-table.thead>
                <x-table.tbody class="dark:border-gray-500">
                    @foreach ($this->encounters as $encounter)
                        <x-table.row class="bg-white dark:bg-gray-700 dark:text-white cursor-pointer"
                            wire:click="filterDate({{ $encounter }})">
                            <x-table.tbody-cell :item="$encounter->chief_complaint ?? '--'" />
                            <x-table.tbody-cell :item="$encounter->encounter_date ?? '--'" class="font-bold" />
                        </x-table.row>
                    @endforeach
                </x-table.tbody>
            </x-table>
        @endif
        @if ($this->encounter && !$this->show)
            <x-table for="encounter">
                <x-table.tbody class="dark:border-gray-500">
                    <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                        <x-table.thead-cell :title="__('Chief Complaint')" class="text-left" />
                        <x-table.tbody-cell :item="$this->encounter->chief_complaint ?? '--'" />

                        <x-table.thead-cell :title="__('Encounter Date')" class="text-left" />
                        <x-table.tbody-cell :item="$this->encounter->encounter_date ?? '--'" class="font-bold" />
                    </x-table.row>
                    <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                        <x-table.thead-cell :title="__('Notes')" class="text-left" />
                        <x-table.tbody-cell :item="$this->encounter->notes ?? '--'" colspan="3" />
                    </x-table.row>
                </x-table.tbody>
            </x-table>

            <livewire:patient.encounter.physical-examination :encounter="$this->encounter" />
            <livewire:patient.encounter.diagnostic-test :encounter="$this->encounter" />
        @endif
    </fieldset>
    <x-modal name="create-new-encounter" :show="$errors->isNotEmpty()">
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="w-1/3" x-data x-init="flatpickr($refs.dateInput, {
                altInput: true,
                altFormat: 'F j, Y',
                dateFormat: 'Y-m-d'
            })">
                <x-input-label for="encounter_date" value="{{ __('Encounter Date') }}" />
                <x-text-input wire:model="form.encounter_date" x-ref="dateInput" id="encounter_date"
                    name="encounter_date" type="text" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.encounter_date')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="chief_complaint" value="{{ __('Chief Complaint') }}" />
                <x-textarea wire:model="form.chief_complaint" id="chief_complaint" name="notes"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.chief_complaint')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="notes" value="{{ __('Notes') }}" />
                <x-textarea wire:model="form.notes" id="notes" name="notes"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.notes')" class="mt-2" />
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
