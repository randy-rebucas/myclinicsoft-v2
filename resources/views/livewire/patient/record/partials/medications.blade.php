<?php

use App\Models\Medication;
use App\Models\Queue;
use App\Events\QueueUpdated;
use function Livewire\Volt\{state, mount, rules, computed, on};

state(['patient', 'encounter', 'selectedPrescription', 'doctor', 'queue', 'medications']);

mount(function ($patient, $encounter, $queue) {
    $this->patient = $patient;
    $this->encounter = $encounter;
    $this->queue = $queue;
    $this->doctor = Auth::user()->doctor;

    $this->medications = Medication::where('patient_id', $patient->id)->where('encounter_id', $encounter->id)->get();
});

$completeMedication = function () {
    $this->queue->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated("Queue {$this->queue->queue_number} is now completed!", 'completed'))->toOthers();

    $this->dispatch('queue-completed');
};

$showModal = function ($type, $title, $form) {
    $this->dispatch('show-modal', ['type' => $type, 'title' => $title, 'form' => $form, 'encounter' => $this->encounter, 'record' => null]);
};

// Event Handlers
on([
    'medication-refreshed' => function () {
        $this->dispatch('refresh');
        $this->dispatch('close-modal', 'medications');
        $this->medications = Medication::where('patient_id', $this->patient->id)->where('encounter_id', $this->encounter->id)->get();
    },
    'encounter-refreshed' => function () {
        $this->medications = Medication::where('patient_id', $this->patient->id)->where('encounter_id', $this->encounter->id)->get();
    }
]);
?>
<div x-data="{ showPrintModal: false }" class="bg-white shadow-lg rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Current Medications</h3>
            <div class="flex space-x-2">
                <button wire:click="$refresh"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                    Refresh
                </button>
                <button wire:click="showModal('add', 'Add New Medication', 'medications')"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    Add Medication
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @if ($this->medications->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <x-heroicon-o-beaker class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                    <p>No medications recorded</p>
                </div>
            @else
                @foreach ($this->medications as $medication)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div class="flex-grow space-y-4">
                                @foreach ($medication->prescription_items as $item)
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="flex items-center">
                                                <p class="font-medium text-gray-900">{{ $item['medication_name'] }}</p>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">{{ $item['dosage'] }} -
                                                {{ $item['frequency'] }}</p>
                                            @if (isset($item['instructions']))
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <span class="font-medium">Instructions:</span>
                                                    {{ $item['instructions'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="border-t flex items-center justify-end pt-2 space-x-3">
                                    <a href="{{ route('prescription', ['medicationId' => $medication->id]) }}"
                                        target="_blank"
                                        class="inline-flex items-center text-sm text-gray-600 hover:text-green-600 transition-colors">
                                        <x-heroicon-o-printer class="w-4 h-4 mr-1" />
                                        Print
                                    </a>
                                    <button
                                        @click="if (confirm('Are you sure you want to complete this medication?')) $wire.completeMedication()"
                                        class="inline-flex items-center text-sm text-gray-600 hover:text-green-600 transition-colors">
                                        <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                        Complete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
