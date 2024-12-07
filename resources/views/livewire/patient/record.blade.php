<?php

use App\Models\Patient;
use App\Models\Allergy;
use App\Models\FamilyHistory;
use App\Models\DiagnosticTest;
use App\Models\Immunization;
use App\Models\MedicalCondition;
use App\Models\Medication;
use App\Models\PhysicalExamination;
use App\Models\Vital;
use App\Models\Encounter;

use function Livewire\Volt\{state, mount, computed, on};

// State Management
state(['queue', 'patient', 'encounter', 'showModal' => false, 'modalType' => null, 'modalForm' => null, 'modalTitle' => null]);

// Lifecycle Hooks
mount(function ($queue) {
    $this->queue = $queue;
    $this->patient = $queue->patient;
});

// Event Handlers
on([
    'encounter-created' => function ($encounter) {
        $this->encounter = $encounter;
    },
    'show-modal' => function ($data) {
        $this->showModal = true;
        $this->modalType = $data['type'];
        $this->modalTitle = $data['title'];
        $this->modalForm = $data['form'];
    },
    'close-modal' => function () {
        $this->showModal = false;
    },
]);

// Computed Properties
$medications = computed(fn() => Medication::where('patient_id', $this->patient->id)->get() ?? collect());

?>

<!-- Rest of your Blade template remains the same -->
<div class="w-full mx-auto p-8">

    <!-- Patient Information Card -->
    <div class="bg-white shadow-lg rounded-lg mb-8">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Patient Information</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium">{{ $patient->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date of Birth</p>
                    <p class="font-medium">
                        {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Patient ID</p>
                    <p class="font-medium">{{ $patient->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-4 gap-8">
        <!-- Left Column (col-span-3) -->
        <div class="col-span-3 space-y-8">
            <!-- Encounters Section -->
            <livewire:patient.record.partials.encounters :patient="$this->patient" />

            <!-- Medications Section -->
            @if ($this->encounter)
                <livewire:patient.record.partials.medications :medications="$this->medications" :queue="$queue" />
            @endif
        </div>

        <!-- Right Column (col-span-1) -->
        <div class="col-span-1">
            <div class="bg-white shadow-lg rounded-lg p-4">
                <!-- Sidebar Navigation Items -->
                <livewire:patient.record.partials.sidebar-items :patient="$this->patient" />
            </div>
        </div>
    </div>

    <!-- Modal Components -->
    @if ($this->showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40" wire:transition.duration.300ms
            wire:transition.in.opacity.0.opacity.100 wire:transition.out.opacity.100.opacity.0>
        </div>
    @endif

    <!-- Sliding Modal -->
    @if ($this->showModal)
        <div wire:transition.duration.300ms wire:transition.in.transform.translate-x-full.translate-x-0
            wire:transition.out.transform.translate-x-0.translate-x-full
            class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-50 overflow-y-auto">

            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">{{ $this->modalTitle }}</h3>
                    @if ($this->showModal)
                        <button class="text-gray-400 hover:text-gray-500">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    @endif
                </div>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-4">
                @if ($this->modalType === 'add' || $this->modalType === 'edit')
                    @switch($this->modalForm)
                        @case('allergies')
                            <livewire:patient.record.forms.allergy-form :patient="$this->patient" :record="null" />
                        @break

                        @case('family-history')
                            <livewire:patient.record.forms.family-history-form :patient="$this->patient" :record="null" />
                        @break

                        @case('diagnostic-test')
                            <livewire:patient.record.forms.diagnostic-test-form :patient="$this->patient" :record="null" />
                        @break

                        @case('immunization')
                            <livewire:patient.record.forms.immunization-form :patient="$this->patient" :record="null" />
                        @break

                        @case('medical-condition')
                            <livewire:patient.record.forms.medical-condition-form :patient="$this->patient" :record="null" />
                        @break

                        @case('vital')
                            <livewire:patient.record.forms.vital-sign-form :patient="$this->patient" :record="null" />
                        @break

                        @case('encounter')
                            <livewire:patient.record.forms.encounter-form :patient="$this->patient" :record="null" />
                        @break

                        @default
                    @endswitch
                @endif
            </div>
        </div>
    @endif
</div>
