<?php

use App\Models\Allergy;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'allergen' => '',
    'reaction' => '',
    'severity' => ''
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;

    if ($record) {
        $this->record = $record;
        $this->allergen = $record->allergen;
        $this->reaction = $record->reaction;
        $this->severity = $record->severity;
    }
});

rules([
    'allergen' => 'required|string',
    'reaction' => 'required|string',
    'severity' => 'required|in:Low,Medium,High',
]);

$save = function () {
    $validated = $this->validate();

    if ($this->record) {
        $this->record->update($validated);
    } else {
        Allergy::create([
            'patient_id' => $this->patient->id,
            ...$validated
        ]);
    }

    $this->dispatch('close-modal');
};

?>

<form wire:submit.prevent="save" class="space-y-4">
    <div>
        <label for="allergen" class="block text-sm font-medium text-gray-700">Allergen</label>
        <input type="text" wire:model="allergen" id="allergen"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('allergen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="reaction" class="block text-sm font-medium text-gray-700">Reaction</label>
        <input type="text" wire:model="reaction" id="reaction"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('reaction') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="severity" class="block text-sm font-medium text-gray-700">Severity</label>
        <select wire:model="severity" id="severity"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">Select Severity</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
        @error('severity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" x-data @click="$dispatch('close-modal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            Save
        </button>
    </div>
</form>
