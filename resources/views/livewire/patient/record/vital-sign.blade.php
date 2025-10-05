<?php

use App\Models\Vital;
use function Livewire\Volt\{state, computed, on, mount};

state(['patient', 'showForm' => false, 'editingVital' => null]);

mount(function ($patient) {
    $this->patient = $patient;
});

// Event Handlers
on([
    'vitals-refreshed' => function () {
        $this->dispatch('refresh');
    },
    'close-modal' => function () {
        $this->showForm = false;
        $this->editingVital = null;
    },
]);

$vitals = computed(fn() => Vital::where('patient_id', $this->patient->id)
    ->orderBy('created_at', 'desc')
    ->get());

$delete = function ($id) {
    Vital::find($id)->delete();
    $this->dispatch('refresh');
};

$edit = function ($vital) {
    $this->editingVital = $vital;
    $this->showForm = true;
};

$addNew = function () {
    $this->editingVital = null;
    $this->showForm = true;
};

?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Vital Signs</h3>
        <button wire:click="addNew"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Vital Signs
        </button>
    </div>

    @if ($this->vitals->count() > 0)
        <div class="grid gap-4">
            @foreach ($this->vitals as $vital)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                Vital Signs Record
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $vital->created_at ? $vital->created_at->format('M d, Y g:i A') : 'N/A' }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="edit({{ $vital->id }})"
                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $vital->id }})"
                                wire:confirm="Are you sure you want to delete this vital signs record?"
                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @if ($vital->blood_pressure)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->blood_pressure }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Blood Pressure</div>
                                <div class="text-xs text-gray-400">mmHg</div>
                            </div>
                        @endif

                        @if ($vital->heart_rate)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->heart_rate }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Heart Rate</div>
                                <div class="text-xs text-gray-400">bpm</div>
                            </div>
                        @endif

                        @if ($vital->temperature)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->temperature }}°
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Temperature</div>
                                <div class="text-xs text-gray-400">°C</div>
                            </div>
                        @endif

                        @if ($vital->respiratory_rate)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->respiratory_rate }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Respiratory Rate</div>
                                <div class="text-xs text-gray-400">breaths/min</div>
                            </div>
                        @endif

                        @if ($vital->oxygen_saturation)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->oxygen_saturation }}%
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Oxygen Saturation</div>
                                <div class="text-xs text-gray-400">%</div>
                            </div>
                        @endif

                        @if ($vital->blood_sugar)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $vital->blood_sugar }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Blood Sugar</div>
                                <div class="text-xs text-gray-400">mg/dL</div>
                            </div>
                        @endif
                    </div>

                    @if ($vital->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Notes:</strong> {{ $vital->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No vital signs recorded</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding the first vital signs record.</p>
            <div class="mt-6">
                <button wire:click="addNew"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Vital Signs
                </button>
            </div>
        </div>
    @endif

    <!-- Modal for adding/editing vital signs -->
    @if ($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ show: @entangle('showForm') }" x-show="show">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ $editingVital ? 'Edit Vital Signs' : 'Add New Vital Signs' }}
                        </h3>
                        <button wire:click="$dispatch('close-modal')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <livewire:patient.record.forms.vital-sign-form :patient="$patient" :record="$editingVital" />
                </div>
            </div>
        </div>
    @endif
</div>
