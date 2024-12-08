<?php

use App\Models\Medication;
use function Livewire\Volt\{state, mount, rules};

state(['patient', 'record' => null, 'encounter', 'prescription_items' => [['medication_name' => '', 'dosage' => '', 'frequency' => '']], 'notes' => '', 'isSubmitting' => false]);

mount(function ($patient, $record = null, $encounter = null) {
    $this->patient = $patient;
    $this->encounter = $encounter;
    if ($record) {
        $this->record = $record;
        $this->prescription_items = $record->prescription_items ?: [['medication_name' => '', 'dosage' => '', 'frequency' => '']];
        $this->notes = $record->notes;
    }
});

rules([
    'prescription_items.*.medication_name' => 'required|string',
    'prescription_items.*.dosage' => 'required|string',
    'prescription_items.*.frequency' => 'required|string',
    'notes' => 'nullable|string',
]);

$addPrescriptionItem = function () {
    $this->prescription_items[] = ['medication_name' => '', 'dosage' => '', 'frequency' => ''];
};

$removePrescriptionItem = function ($index) {
    if (count($this->prescription_items) > 1) {
        unset($this->prescription_items[$index]);
        $this->prescription_items = array_values($this->prescription_items);
    }
};

$save = function () {
    $this->isSubmitting = true;
    try {
        $validated = $this->validate();

        if ($this->record) {
            $this->record->update($validated);
        } else {
            Medication::create([
                'patient_id' => $this->patient->id,
                'encounter_id' => $this->encounter->id,
                ...$validated,
            ]);
        }

        $this->dispatch('medication-refreshed');
        $this->dispatch('close-modal');

    } catch (\Exception $e) {
        $this->addError('save', 'Failed to save medication record.');
    } finally {
        $this->isSubmitting = false;
    }
};

?>

<div class="flex flex-col h-full max-h-[80vh]">
    <style>
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 #F1F5F9;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #CBD5E1;
            border-radius: 4px;
            border: 2px solid #F1F5F9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94A3B8;
        }
    </style>

    <form wire:submit="save" class="flex flex-col h-full overflow-y-auto custom-scrollbar">
        <div class="flex-1 py-4 space-y-4 ">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Prescription Items</label>

                @foreach ($prescription_items as $index => $item)
                    <div class="space-y-2 p-3 border rounded-md bg-gray-50 relative">
                        @if (count($prescription_items) > 1)
                            <button type="button" wire:click="removePrescriptionItem({{ $index }})"
                                class="absolute -top-3 right-2 text-white bg-red-500 hover:bg-red-600 rounded-full p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif

                        <div>
                            <label class="block text-xs font-medium text-gray-700">Medication Name</label>
                            <input type="text" wire:model="prescription_items.{{ $index }}.medication_name"
                                class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error("prescription_items.{$index}.medication_name")
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Dosage</label>
                                <input type="text" wire:model="prescription_items.{{ $index }}.dosage"
                                    class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error("prescription_items.{$index}.dosage")
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Frequency</label>
                                <input type="text" wire:model="prescription_items.{{ $index }}.frequency"
                                    class="mt-0.5 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error("prescription_items.{$index}.frequency")
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addPrescriptionItem"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Add Prescription Item
                </button>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea wire:model="notes" id="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                @error('notes')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
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
</div>
