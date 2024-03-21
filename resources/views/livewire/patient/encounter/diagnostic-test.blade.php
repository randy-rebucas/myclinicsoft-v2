<?php

use App\Models\DiagnosticTest;
use App\Livewire\Forms\DiagnosticTestForm;
use function Livewire\Volt\{state, form, mount, computed};

state('encounter');

form(DiagnosticTestForm::class);

mount(function () {
    $this->form->encounter_id = $this->encounter->id;
});

$diagnostic_test = computed(function () {
    return DiagnosticTest::where('encounter_id', $this->encounter->id)
        ->orderBy('test_date', 'desc')
        ->latest()
        ->first();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-diagnostic-test');

    $this->dispatch('refresh');
};
?>

<div class="relative">
    <button type="button" class="btn btn-info m-1 font-medium underline absolute right-0 top-2" x-data=""
        x-on:click="$dispatch('open-modal', 'create-new-diagnostic-test')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path
                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
    </button>
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Diagnostic Test') }}</h3>
    <x-table for="diagnostic_test">
        <x-table.tbody class="dark:border-gray-500">
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Test Name')" class="text-left" />
                <x-table.tbody-cell :item="$this->diagnostic_test->test_name ?? '--'" />

                <x-table.thead-cell :title="__('Date')" class="text-left" />
                <x-table.tbody-cell :item="$this->diagnostic_test->test_date ?? '--'" class="font-bold" />
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Results')" class="text-left" />
                <x-table.tbody-cell :item="$this->diagnostic_test->results ?? '--'" colspan="3" />
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Notes')" class="text-left" />
                <x-table.tbody-cell :item="$this->diagnostic_test->notes ?? '--'" colspan="3" />
            </x-table.row>
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-diagnostic-test" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/2">
                    <x-input-label for="test_name" value="{{ __('Test Name') }}" />
                    <x-text-input wire:model="form.test_name" id="test_name" name="test_name" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.test_name')" class="mt-2" />
                </div>
                <div class="w-1/2" x-data x-init="flatpickr($refs.dateInput, {
                    altInput: true,
                    altFormat: 'F j, Y',
                    dateFormat: 'Y-m-d'
                })">
                    <x-input-label for="test_date" value="{{ __('Test Date') }}" />
                    <x-text-input wire:model="form.test_date" x-ref="dateInput" id="test_date" name="test_date"
                        type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.test_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="results" value="{{ __('Results') }}" />
                <x-textarea wire:model="form.results" id="results" name="results"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.results')" class="mt-2" />
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
