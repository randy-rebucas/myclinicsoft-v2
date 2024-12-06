<?php

use App\Models\FamilyHistory;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'condition' => '',
    'relationship' => '',
    'notes' => '',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->condition = $record->condition;
        $this->relationship = $record->relationship;
        $this->notes = $record->notes;
    }
});

rules([
    'condition' => 'required|string|max:255',
    'relationship' => 'required|string|max:255',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->validate();

    $data = [
        'condition' => $this->condition,
        'relationship' => $this->relationship,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        FamilyHistory::create([
            'patient_id' => $this->patient->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal');
};

?>

<form wire:submit.prevent="save" class="space-y-4">
    <div>
        <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
        <input type="text" wire:model="condition" id="condition"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('condition') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="relationship" class="block text-sm font-medium text-gray-700">Relation</label>
        <select wire:model="relationship" id="relationship"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">Select Relation</option>
            <option value="father">Father</option>
            <option value="mother">Mother</option>
            <option value="sibling">Sibling</option>
            <option value="grandparent">Grandparent</option>
            <option value="other">Other</option>
        </select>
        @error('relationship') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea wire:model="notes" id="notes" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end gap-2">
        <button type="button" wire:click="$dispatch('close-modal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Save
        </button>
    </div>
</form>
