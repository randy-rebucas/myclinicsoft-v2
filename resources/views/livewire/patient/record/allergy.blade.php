<?php

use App\Models\Allergy;
use App\Livewire\Forms\AllergyForm;
use function Livewire\Volt\{state, form, mount, computed};

state([
    'patient',
    'severity_levels' => [
        'critical' => 'Critical',
        'major' => 'Major',
        'minor' => 'Minor',
    ],
]);

form(AllergyForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$allergies = computed(function () {
    return Allergy::where('patient_id', $this->patient->id)->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', ['record_type' => 'allergies']);
};

$delete = function (Allergy $allergy) {
    $allergy->delete();

    $this->dispatch('refresh', ['record_type' => 'allergy']);
};
?>

<div>
    <form wire:submit="create">
        <div class="mb-4">
            <x-input-label for="allergen" value="{{ __('Allergen') }}" class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.allergen" id="allergen" name="allergen" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.allergen')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="reaction" value="{{ __('Reaction') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input wire:model.live="form.reaction" id="reaction" name="reaction" type="text"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.reaction')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="severity" value="{{ __('Severity') }}"
                class="block text-sm font-medium text-gray-700" />
            <x-select wire:model.live="form.severity" id="severity" name="severity" :options="$severity_levels"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
            <x-input-error :messages="$errors->get('form.severity')" class="mt-2" />
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
