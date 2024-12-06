<?php

use App\Models\FamilyHistory;
use App\Livewire\Forms\FamilyHistoryForm;
use function Livewire\Volt\{state, form, mount, computed};

state([
    'patient',
    'relations' => [
        'family' => 'Family',
        'friend' => 'Friend',
        'work' => 'Work',
    ],
]);

form(FamilyHistoryForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'family-history']);
};

$delete = function (FamilyHistory $family_history) {
    $family_history->delete();

    $this->dispatch('refresh');
};

?>

<div>

    <form wire:submit="create">

        <div class="mb-4">
            <x-input-label for="relationship" value="{{ __('Relationship') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-select wire:model.live="form.relationship" id="relationship" name="relationship" :options="$relations"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.relationship')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="condition" value="{{ __('Condition') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.condition" id="condition" name="condition" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.condition')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="notes" value="{{ __('Notes') }}" class="block text-sm font-medium text-gray-700" />
            <x-textarea wire:model.live="form.notes" id="notes" name="notes"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></x-textarea>
            <x-input-error :messages="$errors->get('form.notes')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            {{ __('Save') }}
        </button>
    </form>
</div>
