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
        ->where('encounter_date', Carbon::today())
        ->get()
        ->first();
    if ($encounter) {
        $this->form->setEncounterId($encounter->id);
    }
    $this->form->setPatientId($this->patient->id);
});

$medical_conditions = computed(function () {
    $query = MedicalCondition::query();
    if ($this->form->encounter_id) {
        $query->where('encounter_id', $this->form->encounter_id);
    }
    return $query->where('patient_id', $this->patient->id)->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-medical-condition');

    $this->dispatch('refresh');
};

$delete = function (MedicalCondition $medical_condition) {
    $medical_condition->delete();

    $this->dispatch('refresh');
};
?>

<div>
    <x-table for="medical-condition">
        <x-table.thead>
            <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                <x-table.thead-cell :title="__('Condition Name')" class="text-left" />
                <x-table.thead-cell :title="__('Treatment Plan')" class="text-left" />
                <x-table.thead-cell :title="__('Diagnosis Date')" class="text-left" />
                <x-table.thead-cell :title="__('Status')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    @if ($this->form->encounter_id)
                        <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                            x-on:click="$dispatch('open-modal', 'create-new-medical-condition')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                            </svg>
                        </button>
                    @endif
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="dark:border-gray-500">
            @forelse ($this->medical_conditions as $medical_condition)
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$medical_condition->condition_name" />
                    <x-table.tbody-cell :item="$medical_condition->treatment_plan" />
                    <x-table.tbody-cell :item="$medical_condition->diagnosis_date" />
                    <x-table.tbody-cell :item="$medical_condition->status" class="uppercase" />
                    <x-table.tbody-cell :item="$medical_condition->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $medical_condition->id }}')">
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
                    <x-table.tbody-cell :item="$medical_condition->notes ?? '--'" colspan="5" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No medical condition record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-medical-condition" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/3">
                    <x-input-label for="condition_name" value="{{ __('Condition Name') }}" />
                    <x-text-input wire:model="form.condition_name" id="condition_name" name="condition_name"
                        type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.condition_name')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="diagnosis_date" value="{{ __('Diagnose Date') }}" />
                    <x-text-input wire:model="form.diagnosis_date" id="diagnosis_date"
                        name="diagnosis_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.diagnosis_date')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="status" value="{{ __('Status') }}" />
                    <x-select wire:model="form.status" id="status" name="status" :options="$statuses"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('form.status')" class="mt-2" />
                </div>
            </div>
            <div class="mt-4">
                <x-input-label for="treatment_plan" value="{{ __('Treatment Plan') }}" />
                <x-text-input wire:model="form.treatment_plan" id="treatment_plan" name="treatment_plan" type="text"
                    class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.treatment_plan')" class="mt-2" />
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
