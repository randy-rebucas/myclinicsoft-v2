<?php

use App\Models\DiagnosticTest;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'test_name' => '',
    'results' => '',
    'test_date' => '',
    'notes' => '',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->test_name = $record->test_name;
        $this->results = $record->results;
        $this->test_date = $record->test_date;
        $this->notes = $record->notes;
    }
});

rules([
    'test_name' => 'required|string|max:255',
    'results' => 'required|string',
    'test_date' => 'required|date',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->validate();

    $data = [
        'test_name' => $this->test_name,
        'results' => $this->results,
        'test_date' => $this->test_date,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        DiagnosticTest::create([
            'patient_id' => $this->patient->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal');
};

?>

<form wire:submit.prevent="save" class="space-y-4">
    <div>
        <label for="test_name" class="block text-sm font-medium text-gray-700">Test Name</label>
        <input type="text" wire:model="test_name" id="test_name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('test_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="results" class="block text-sm font-medium text-gray-700">Result</label>
        <textarea wire:model="results" id="results" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
        @error('results') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="test_date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" wire:model="test_date" id="test_date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('test_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
