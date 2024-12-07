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
    'administrators' => [],
    'isSubmitting' => false,
]);

mount(function ($patient, $record = null) {
    $this->patient = $patient;
    $this->administrators = [
        'Physician',
        'Nurse',
        'Nurse Practitioner',
        'Physician Assistant',
        'Medical Assistant',
        'Pharmacist',
        'Public Health Official',
        'Other Healthcare Provider',
        'Other',
    ];

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
    $this->isSubmitting = true;
    try {
        $validated = $this->validate();

        if ($this->record) {
            $this->record->update($validated);
        } else {
            Immunization::create([
                'patient_id' => $this->patient->id,
                ...$validated,
            ]);
        }

        $this->dispatch('immunizations-refreshed');
        $this->dispatch('close-modal');
    } catch (\Exception $e) {
        $this->addError('save', 'Failed to save immunization record.');
    } finally {
        $this->isSubmitting = false;
    }
};

?>

<form wire:submit="save" class="space-y-4">
    <div>
        <label for="vaccine_name" class="block text-sm font-medium text-gray-700">Vaccine Name</label>
        <input type="text" wire:model="vaccine_name" id="vaccine_name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('vaccine_name')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="date_administered" class="block text-sm font-medium text-gray-700">Date Administered</label>
        <input type="date" wire:model="date_administered" id="date_administered"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('date_administered')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="administrator" class="block text-sm font-medium text-gray-700">Administrator</label>
        <select wire:model="administrator" id="administrator"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">Select Administrator</option>
            @foreach ($administrators as $admin)
                <option value="{{ $admin }}">{{ $admin }}</option>
            @endforeach
        </select>
        @error('administrator')
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
        <button type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
            <span wire:loading.remove>Save</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>
</form>
