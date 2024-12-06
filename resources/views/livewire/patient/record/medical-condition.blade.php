<?php

use Carbon\Carbon;
use App\Models\Encounter;
use App\Models\MedicalCondition;
use App\Livewire\Forms\MedicalConditionForm;
use function Livewire\Volt\{state, form, mount, on, computed};

state([
    'patient',
    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
]);

form(MedicalConditionForm::class);

on([
    'set-encounter' => function ($encounterId) {
        $this->form->setEncounterId($encounterId);
    },
]);

mount(function () {
    $encounter = Encounter::where('patient_id', $this->patient->id)
        ->where('encounter_date', now()->toDateString())
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

    $this->dispatch('close-modal', ['record_type' => 'medical-condition']);
};

$delete = function (MedicalCondition $medical_condition) {
    $medical_condition->delete();

    $this->dispatch('refresh');
};
?>

<div>

    <form wire:submit="create">

        <div class="mb-4">
            <x-input-label for="condition_name" value="{{ __('Condition Name') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.condition_name" id="condition_name" name="condition_name" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.condition_name')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="diagnosis_date" value="{{ __('Diagnose Date') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.diagnosis_date" id="diagnosis_date" name="diagnosis_date" type="date"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.diagnosis_date')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="status" value="{{ __('Status') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-select wire:model.live="form.status" id="status" name="status" :options="$statuses"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.status')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="treatment_plan" value="{{ __('Treatment Plan') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.treatment_plan" id="treatment_plan" name="treatment_plan" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.treatment_plan')" class="mt-2" />
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
