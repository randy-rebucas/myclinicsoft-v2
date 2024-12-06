<?php

use App\Models\DiagnosticTest;
use App\Livewire\Forms\DiagnosticTestForm;
use function Livewire\Volt\{state, form, mount, computed};

state('patient');

form(DiagnosticTestForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'diagnostic-test']);
};

$delete = function (DiagnosticTest $diagnostic_test) {
    $diagnostic_test->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <form wire:submit="create">

        <div class="mb-4">
            <x-input-label for="test_name" value="{{ __('Test Name') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.test_name" id="test_name" name="test_name" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.test_name')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="test_date" value="{{ __('Test Date') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.test_date" id="test_date" name="test_date" type="date"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.test_date')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="results" value="{{ __('Results') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-textarea wire:model.live="form.results" id="results" name="results"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></x-textarea>
            <x-input-error :messages="$errors->get('form.results')" class="mt-2" />
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
