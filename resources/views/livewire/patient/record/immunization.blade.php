<?php

use App\Models\Immunization;
use App\Livewire\Forms\ImmunizationForm;
use function Livewire\Volt\{state, form, mount, computed};

state([
    'patient',
    'administrators' => [
        'physician' => 'Physician',
        'nurse' => 'Nurse',
    ],
]);

form(ImmunizationForm::class);

mount(function () {
    $this->form->date_administered = now()->toDateString();
    $this->form->patient_id = $this->patient->id;
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'immunization']);
};

$delete = function (Immunization $immunization) {
    $immunization->delete();

    $this->dispatch('refresh');
};
?>

<div>
    <form wire:submit="create">
        <div class="mb-4">
            <x-input-label for="vaccine_name" value="{{ __('Vaccine Name') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.vaccine_name" id="vaccine_name" name="vaccine_name" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.vaccine_name')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="administrator" value="{{ __('Administrator') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-select wire:model.live="form.administrator" id="administrator" name="administrator" :options="$administrators"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 " />
            <x-input-error :messages="$errors->get('form.administrator')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="date_administered" value="{{ __('Date Administered') }}" />
            <x-text-input wire:model.live="form.date_administered" id="date_administered" name="date_administered"
                type="date"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.date_administered')" class="mt-2" />
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
