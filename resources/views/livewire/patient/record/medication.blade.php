<?php

use App\Models\Encounter;
use App\Models\Medication;
use Carbon\Carbon;
use App\Livewire\Forms\MedicationForm;
use function Livewire\Volt\{state, form, on, mount, computed};

state('patient');

form(MedicationForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

on([
    'encounter-created' => function ($encounterId) {
        $this->form->setEncounterId($encounterId);
    },
]);

mount(function () {
    $encounter = Encounter::where('patient_id', $this->patient->id)
        ->where('encounter_date', Carbon::today())
        ->get()
        ->first();
    if ($encounter) {
        $this->form->setEncounterId($encounter->id);
    }
    $this->form->setPatientId($this->patient->id);
});

$medications = computed(function () {
    return Medication::where('patient_id', $this->patient->id)->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-medication');

    $this->dispatch('refresh');
};

$delete = function (Medication $medication) {
    $medication->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <x-table for="medication">
        <x-table.thead>
            <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                <x-table.thead-cell :title="__('Medication Name')" class="text-left" />
                <x-table.thead-cell :title="__('Dosage')" class="text-left" />
                <x-table.thead-cell :title="__('Frequency')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click="$dispatch('open-modal', 'create-new-medication')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="dark:border-gray-500">
            @forelse ($this->medications as $medication)
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$medication->medication_name" />
                    <x-table.tbody-cell :item="$medication->dosage" />
                    <x-table.tbody-cell :item="$medication->frequency" />
                    <x-table.tbody-cell :item="$medication->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $medication->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </x-table.tbody-cell>
                </x-table.row>
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.thead-cell :title="__('Notes')" class="text-left" />
                    <x-table.tbody-cell :item="$medication->notes ?? '--'" colspan="3" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No medication record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-medication" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-3/4">
                    <x-input-label for="medication_name" value="{{ __('Medication Name') }}" />
                    <x-text-input wire:model="form.medication_name" id="medication_name" name="medication_name"
                        type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.medication_name')" class="mt-2" />
                </div>
                <div class="w-1/4">
                    <x-input-label for="dosage" value="{{ __('Dosage ') }}" />
                    <x-text-input wire:model="form.dosage" id="dosage" name="dosage" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.dosage')" class="mt-2" />
                </div>
            </div>
            <div class="mt-4">
                <x-input-label for="frequency" value="{{ __('Frequency') }}" />
                <x-text-input wire:model="form.frequency" id="frequency" name="frequency" type="text"
                    class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.frequency')" class="mt-2" />
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
