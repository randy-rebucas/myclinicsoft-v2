<?php

use App\Models\Medication;
use App\Models\Queue;
use App\Events\QueueUpdated;
use Carbon\Carbon;
use function Livewire\Volt\{state, mount, rules, on};

state(['patient']);

mount(function () {
    $this->encounter = $this->patient->encounters()->whereDate('encounter_date', Carbon::today()->format('Y-m-d'))->first();
});

// Event Handlers
on([
    'encounter-refreshed' => function () {
        $this->refresh();
    }
]);
$showModal = function ($type, $title, $form) {
    $this->dispatch('show-modal', ['type' => $type, 'title' => $title, 'form' => $form]);
};
?>

<div class="bg-white shadow-lg rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Recent Encounters</h3>
            <div class="flex gap-2">
                <button wire:click="showModal('all', 'All Encounters', 'encounter')"
                    class="text-blue-600 hover:text-blue-700 text-sm">
                    View All
                </button>
                <button wire:click="showModal('add', 'Add New Encounter', 'encounter')"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                    New Encounter
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @if ($this->encounter != null)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">
                                {{ $this->encounter->encounter_date->format('M d, Y') }}
                            </p>
                            <p class="text-sm text-gray-600">Dr. {{ $this->encounter->doctor->name }}</p>
                        </div>
                        <button
                            @click="showModal = true; modalType = 'detail'; modalTitle = 'View Encounter'; modalForm = 'encounter'; selectedRecord = {{ $this->encounter->id }}"
                            class="text-blue-600 hover:text-blue-700 text-sm">
                            View Details
                        </button>
                    </div>
                </div>
            @else
                <div class="border rounded-lg p-4">
                    <p class="text-sm text-gray-600">No encounters found for today.</p>
                </div>
            @endif
        </div>
    </div>
</div>
