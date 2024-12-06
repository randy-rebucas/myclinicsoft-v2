<?php

use App\Models\Immunization;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'vaccine_name' => '',
    'date_administered' => '',
    'administrator' => '',
    'notes' => '',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->vaccine_name = $record->vaccine_name;
        $this->date_administered = $record->date_administered;
        $this->administrator = $record->administrator;
        $this->notes = $record->notes;
    }
});

rules([
    'vaccine_name' => 'required|string|max:255',
    'date_administered' => 'required|date',
    'administrator' => 'required|string|max:255',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->validate();

    $data = [
        'vaccine_name' => $this->vaccine_name,
        'date_administered' => $this->date_administered,
        'administrator' => $this->administrator,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        Immunization::create([
            'patient_id' => $this->patient->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal', 'immunizations');
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="vaccine_name" class="block text-sm font-medium text-gray-700">Vaccine Name</label>
        <input type="text" wire:model="vaccine_name" id="vaccine_name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('vaccine_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="date_administered" class="block text-sm font-medium text-gray-700">Date Administered</label>
        <input type="date" wire:model="date_administered" id="date_administered"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('date_administered') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="administrator" class="block text-sm font-medium text-gray-700">Administrator</label>
        <input type="text" wire:model="administrator" id="administrator"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('administrator') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
