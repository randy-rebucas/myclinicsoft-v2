<?php

use App\Models\Encounter;
use App\Models\Medication;
use Carbon\Carbon;
use App\Livewire\Forms\MedicationForm;
use function Livewire\Volt\{state, form, on, mount, computed};

state('patient');

form(MedicationForm::class);

on([
    'set-encounter' => function ($encounterId) {
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

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'medication']);
};

$delete = function (Medication $medication) {
    $medication->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <form wire:submit="create">
        <div class="mb-4">
            <x-input-label for="medication_name" value="{{ __('Medication Name') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.medication_name" id="medication_name" name="medication_name" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.medication_name')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="dosage" value="{{ __('Dosage ') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.dosage" id="dosage" name="dosage" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.dosage')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="frequency" value="{{ __('Frequency') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.frequency" id="frequency" name="frequency" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.frequency')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="notes" value="{{ __('Notes') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-textarea wire:model.live="form.notes" id="notes" name="notes"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></x-textarea>
            <x-input-error :messages="$errors->get('form.notes')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            {{ __('Save') }}
        </button>
    </form>
</div>
