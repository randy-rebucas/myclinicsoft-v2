<?php

use App\Models\Vital;
use function Livewire\Volt\{state, mount, rules};

state([
    'patient',
    'record' => null,
    'date' => '',
    'blood_pressure' => '',
    'heart_rate' => '',
    'respiratory_rate' => '',
    'temperature' => '',
    'oxygen_saturation' => '',
    'notes' => '',
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    if ($record) {
        $this->record = $record;
        $this->date = $record->date;
        $this->blood_pressure = $record->blood_pressure;
        $this->heart_rate = $record->heart_rate;
        $this->respiratory_rate = $record->respiratory_rate;
        $this->temperature = $record->temperature;
        $this->oxygen_saturation = $record->oxygen_saturation;
        $this->notes = $record->notes;
    }
});

rules([
    'date' => 'required|date',
    'blood_pressure' => 'required|string|max:255',
    'heart_rate' => 'required|numeric|min:0|max:300',
    'respiratory_rate' => 'required|numeric|min:0|max:100',
    'temperature' => 'required|numeric|min:30|max:45',
    'oxygen_saturation' => 'required|numeric|min:0|max:100',
    'notes' => 'nullable|string',
]);

$save = function () {
    $this->validate();

    $data = [
        'date' => $this->date,
        'blood_pressure' => $this->blood_pressure,
        'heart_rate' => $this->heart_rate,
        'respiratory_rate' => $this->respiratory_rate,
        'temperature' => $this->temperature,
        'oxygen_saturation' => $this->oxygen_saturation,
        'notes' => $this->notes,
    ];

    if ($this->record) {
        $this->record->update($data);
    } else {
        Vital::create([
            'patient_id' => $this->patient->id,
            ...$data
        ]);
    }

    $this->dispatch('close-modal');
};

?>

<form wire:submit.prevent="save" class="space-y-4">
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
        <input type="datetime-local" wire:model="date" id="date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="blood_pressure" class="block text-sm font-medium text-gray-700">Blood Pressure (mmHg)</label>
        <input type="text" wire:model="blood_pressure" id="blood_pressure" placeholder="120/80"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('blood_pressure') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="heart_rate" class="block text-sm font-medium text-gray-700">Heart Rate (bpm)</label>
        <input type="number" wire:model="heart_rate" id="heart_rate"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('heart_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Respiratory Rate (breaths/min)</label>
        <input type="number" wire:model="respiratory_rate" id="respiratory_rate"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('respiratory_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
        <input type="number" wire:model="temperature" id="temperature" step="0.1"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('temperature') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700">Oxygen Saturation (%)</label>
        <input type="number" wire:model="oxygen_saturation" id="oxygen_saturation"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('oxygen_saturation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
