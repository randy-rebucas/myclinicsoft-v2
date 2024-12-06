<?php

use App\Models\Encounter;
use function Livewire\Volt\{state, rules, mount};

state([
    'patient',
    'record' => null,
    'encounter_date' => null,
    'chief_complaint' => '',
    'notes' => '',
]);

rules([
    'encounter_date' => 'required|date',
    'chief_complaint' => 'required|string|max:255',
    'notes' => 'required|string',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    $this->record = $record;

    if ($record) {
        $this->encounter_date = $record->encounter_date;
        $this->chief_complaint = $record->chief_complaint;
        $this->notes = $record->notes;
    } else {
        $this->encounter_date = now()->format('Y-m-d');
    }
});

$save = function () {
    $this->validate();

    $data = [
        'encounter_date' => $this->encounter_date,
        'chief_complaint' => $this->chief_complaint,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        Encounter::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => auth()->user()->doctor->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal', ['record_type' => 'encounter']);
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="encounter_date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" wire:model="encounter_date" id="encounter_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('encounter_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="chief_complaint" class="block text-sm font-medium text-gray-700">Chief Complaint</label>
        <textarea wire:model="chief_complaint" id="chief_complaint" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('chief_complaint') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea wire:model="notes" id="notes" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" wire:click="$dispatch('close-modal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            {{ $record ? 'Update' : 'Create' }}
        </button>
    </div>
</form>
