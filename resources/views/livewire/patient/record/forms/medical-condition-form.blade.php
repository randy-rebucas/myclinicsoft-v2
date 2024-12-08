<?php

use App\Models\MedicalCondition;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'condition_name' => '',
    'diagnosis_date' => '',
    'status' => '',
    'treatment_plan' => '',
    'notes' => '',
    'isSubmitting' => false,
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->condition_name = $record->condition_name;
        $this->diagnosis_date = $record->diagnosis_date;
        $this->status = $record->status;
        $this->treatment_plan = $record->treatment_plan;
        $this->notes = $record->notes;
    }
});

rules([
    'condition_name' => 'required|string|max:255',
    'diagnosis_date' => 'required|date',
    'status' => 'required|string|max:255',
    'treatment_plan' => 'nullable|string',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->isSubmitting = true;
    try {
        $validated = $this->validate();

        if ($this->record) {
            $this->record->update($validated);
        } else {
            MedicalCondition::create([
                'patient_id' => $this->patient->id,
                ...$validated,
            ]);
        }

        $this->dispatch('medical-conditions-refreshed');
        $this->dispatch('close-modal');
    } catch (\Exception $e) {
        $this->addError('save', 'Failed to save medical condition record.');
    } finally {
        $this->isSubmitting = false;
    }
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="condition_name" class="block text-sm font-medium text-gray-700">Condition Name</label>
        <input type="text" wire:model="condition_name" id="condition_name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('condition_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="diagnosis_date" class="block text-sm font-medium text-gray-700">Diagnosis Date</label>
        <input type="date" wire:model="diagnosis_date" id="diagnosis_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('diagnosis_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select wire:model="status" id="status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">Select Status</option>
            <option value="Active">Active</option>
            <option value="In Remission">In Remission</option>
            <option value="Resolved">Resolved</option>
            <option value="Chronic">Chronic</option>
        </select>
        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="treatment_plan" class="block text-sm font-medium text-gray-700">Treatment Plan</label>
        <textarea wire:model="treatment_plan" id="treatment_plan" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('treatment_plan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea wire:model="notes" id="notes" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" x-data @click="$dispatch('close-modal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Cancel
        </button>
        <button type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
            <span wire:loading.remove>Save</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>
</form>
