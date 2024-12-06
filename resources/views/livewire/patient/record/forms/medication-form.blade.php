<?php

use App\Models\Medication;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'name' => '',
    'dosage' => '',
    'frequency' => '',
    'start_date' => '',
    'end_date' => '',
    'prescriber' => '',
    'notes' => '',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->name = $record->name;
        $this->dosage = $record->dosage;
        $this->frequency = $record->frequency;
        $this->start_date = $record->start_date;
        $this->end_date = $record->end_date;
        $this->prescriber = $record->prescriber;
        $this->notes = $record->notes;
    }
});

rules([
    'name' => 'required|string|max:255',
    'dosage' => 'required|string|max:255',
    'frequency' => 'required|string|max:255',
    'start_date' => 'required|date',
    'end_date' => 'nullable|date|after_or_equal:start_date',
    'prescriber' => 'nullable|string|max:255',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->validate();

    $data = [
        'name' => $this->name,
        'dosage' => $this->dosage,
        'frequency' => $this->frequency,
        'start_date' => $this->start_date,
        'end_date' => $this->end_date,
        'prescriber' => $this->prescriber,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        Medication::create([
            'patient_id' => $this->patient->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal', 'medications');
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Medication Name</label>
        <input type="text" wire:model="name" id="name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="dosage" class="block text-sm font-medium text-gray-700">Dosage</label>
        <input type="text" wire:model="dosage" id="dosage"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('dosage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency</label>
        <select wire:model="frequency" id="frequency"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">Select Frequency</option>
            <option value="Once daily">Once daily</option>
            <option value="Twice daily">Twice daily</option>
            <option value="Three times daily">Three times daily</option>
            <option value="Four times daily">Four times daily</option>
            <option value="As needed">As needed</option>
            <option value="Weekly">Weekly</option>
            <option value="Monthly">Monthly</option>
        </select>
        @error('frequency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
        <input type="date" wire:model="start_date" id="start_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
        <input type="date" wire:model="end_date" id="end_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="prescriber" class="block text-sm font-medium text-gray-700">Prescriber</label>
        <input type="text" wire:model="prescriber" id="prescriber"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('prescriber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
