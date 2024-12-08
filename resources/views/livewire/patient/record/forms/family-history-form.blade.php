<?php

use App\Models\FamilyHistory;
use function Livewire\Volt\{state, mount, rules};

state(['patient', 'record' => null, 'condition' => '', 'relationship' => '', 'notes' => '', 'isSubmitting' => false]);

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
    $this->isSubmitting = true;
    try {
        $validated = $this->validate();

        if ($this->record) {
            $this->record->update($validated);
        } else {
            FamilyHistory::create([
                'patient_id' => $this->patient->id,
                ...$validated,
            ]);
        }

        $this->dispatch('family-histories-refreshed');
        $this->dispatch('close-modal');
    } catch (\Exception $e) {
        $this->addError('save', 'Failed to save family history record.');
    } finally {
        $this->isSubmitting = false;
    }
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
        <input type="text" wire:model="condition" id="condition"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('condition')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
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
        @error('relationship')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea wire:model="notes" id="notes" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('notes')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" x-data @click="$dispatch('close-modal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit" wire:loading.attr="disabled"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
            <span wire:loading.remove>Save</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>
</form>
