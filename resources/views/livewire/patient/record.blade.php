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

state(['patient', 'encounter', 'showModal' => false, 'modalType' => null, 'selectedRecord' => null, 'modalForm' => null]);

mount(function ($patient) {
    $this->patient = $patient;

    $this->encounter = Encounter::where('patient_id', $this->patient->id)
        ->with('doctor')
        ->orderBy('encounter_date', 'desc')
        ->first();
});

$allergies = computed(function () {
    return Allergy::where('patient_id', $this->patient->id)->get() ?? collect();
});

$familyHistories = computed(function () {
    return FamilyHistory::where('patient_id', $this->patient->id)->get() ?? collect();
});

$diagnosticTests = computed(function () {
    return DiagnosticTest::where('patient_id', $this->patient->id)->get() ?? collect();
});

$medications = computed(function () {
    return Medication::where('patient_id', $this->patient->id)->get() ?? collect();
});

$immunizations = computed(function () {
    return Immunization::where('patient_id', $this->patient->id)->get() ?? collect();
});

$medicalConditions = computed(function () {
    return MedicalCondition::where('patient_id', $this->patient->id)->get() ?? collect();
});

$physicalExaminations = computed(function () {
    return PhysicalExamination::where('patient_id', $this->patient->id)->get() ?? collect();
});

$vitals = computed(function () {
    return Vital::where('patient_id', $this->patient->id)->get() ?? collect();
});

$delete = function ($id, $modelType = 'allergy') {
    $modelClass = match ($modelType) {
        'allergy' => Allergy::class,
        'family-history' => FamilyHistory::class,
        'diagnostic-test' => DiagnosticTest::class,
        'immunization' => Immunization::class,
        'medical-condition' => MedicalCondition::class,
        'medication' => Medication::class,
        'physical-examination' => PhysicalExamination::class,
        'vital' => Vital::class,
        default => null,
    };

    if ($modelClass) {
        $modelClass::find($id)?->delete();
        $this->dispatch('refresh')->self();
    }
};

on([
    'close-modal' => function () {
        $this->showModal = false;
        $this->dispatch('refresh');
    },
]);
?>

<div x-data="{
    showModal: @entangle('showModal'),
    modalType: @entangle('modalType'),
    modalForm: @entangle('modalForm'),
    selectedRecord: @entangle('selectedRecord'),
    modalTitle: ''
}" @close-modal.window="showModal = false" class="w-full mx-auto p-8">
    <!-- Patient Information Section -->
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
                        {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</p>
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
            <!-- Encounter Section -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Encounters</h3>
                        <button
                            @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Encounter'; modalForm = 'encounter'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            New Encounter
                        </button>
                    </div>

                    <div class="space-y-4">
                        @if ($this->encounter)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">
                                            {{ $this->encounter->encounter_date ? $this->encounter->encounter_date->format('M d, Y') : 'N/A' }}
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
                        @endif
                    </div>
                </div>
            </div>

            <!-- Medication Section -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Current Medications</h3>
                        <button
                            @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medication'; modalForm = 'medications'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add Medication
                        </button>
                    </div>

                    <div class="space-y-4">
                        @if ($this->medications->isEmpty())
                            <div class="text-center py-4 text-gray-500">
                                No medications recorded
                            </div>
                        @else
                            @foreach ($this->medications as $medication)
                                <div class="border rounded-lg p-4">
                                    @foreach ($medication->prescription_items as $item)
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-medium">{{ $item['medication_name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $item['dosage'] }} -
                                                    {{ $item['frequency'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (col-span-1) -->
        <div class="col-span-1">
            <div class="bg-white shadow-lg rounded-lg p-4">
                <nav class="space-y-2">
                    <!-- Allergies -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Allergies ({{ $this->allergies->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->allergies as $allergy)
                                    <div
                                        class="text-sm flex justify-between items-start border-l-4 pl-2 {{ match ($allergy->severity) {
                                            'low' => 'border-green-500',
                                            'medium' => 'border-yellow-500',
                                            'high' => 'border-red-500',
                                            default => 'border-gray-500',
                                        } }}">
                                        <div>
                                            <p class="font-medium">{{ $allergy->allergen }}</p>
                                            <p class="text-gray-600">{{ $allergy->reaction }}</p>
                                        </div>
                                        <button wire:click="delete({{ $allergy->id }}, 'allergy')"
                                            wire:confirm="Are you sure you want to delete this allergy record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                    @unless ($loop->last)
                                        <div class="my-2 border-b border-gray-200"></div>
                                    @endunless
                                @endforeach

                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Allergy'; modalForm = 'allergies'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Allergy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Family History -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Family History ({{ $this->familyHistories->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->familyHistories as $history)
                                    <div class="text-sm flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $history->condition }}</p>
                                            <p class="text-gray-600">{{ $history->relation }}</p>
                                        </div>
                                        <button wire:click="delete({{ $history->id }}, 'family-history')"
                                            wire:confirm="Are you sure you want to delete this family history record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Family History'; modalForm = 'family-history'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Family History
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnostic Tests -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Diagnostic Tests ({{ $this->diagnosticTests->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->diagnosticTests as $test)
                                    <div class="text-sm flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $test->test_name }}</p>
                                            <p class="text-gray-600">
                                                {{ $test->date ? $test->date->format('M d, Y') : 'N/A' }}</p>
                                        </div>
                                        <button wire:click="delete({{ $test->id }}, 'diagnostic-test')"
                                            wire:confirm="Are you sure you want to delete this diagnostic test record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Diagnostic Test'; modalForm = 'diagnostic-test'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Diagnostic Test
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Immunizations -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Immunizations ({{ $this->immunizations->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->immunizations as $immunization)
                                    <div class="text-sm flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $immunization->vaccine_name }}</p>
                                            <p class="text-gray-600">
                                                {{ $immunization->date ? $immunization->date->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <button wire:click="delete({{ $immunization->id }}, 'immunization')"
                                            wire:confirm="Are you sure you want to delete this immunization record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Immunization'; modalForm = 'immunization'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Immunization
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Conditions -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Medical Conditions ({{ $this->medicalConditions->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->medicalConditions as $condition)
                                    <div class="text-sm flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">{{ $condition->condition_name }}</p>
                                            <p class="text-gray-600">
                                                {{ $condition->diagnosis_date ? $condition->diagnosis_date->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <button wire:click="delete({{ $condition->id }}, 'medical-condition')"
                                            wire:confirm="Are you sure you want to delete this medical condition record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Condition'; modalForm = 'medical-condition'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Medical Condition
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Vitals -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Vitals ({{ $this->vitals->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach ($this->vitals as $vital)
                                    <div class="text-sm flex justify-between items-start">
                                        <div>
                                            <p class="font-medium">Vitals Check</p>
                                            <p class="text-gray-600">
                                                {{ $vital->recorded_at ? $vital->recorded_at->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <button wire:click="delete({{ $vital->id }}, 'vital')"
                                            wire:confirm="Are you sure you want to delete this vitals record?"
                                            class="text-red-500 hover:text-red-700">
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Vitals'; modalForm = 'vital'"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Vitals
                                </button>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-40"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <!-- Sliding Modal -->
    <div x-show="showModal" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-50">

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900" x-text="modalTitle"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="px-6 py-4">
            <div x-show="modalType === 'detail'">
                <template x-if="modalForm === 'encounter'">
                    <livewire:patient.record.views.encounter-view :record="$encounter" />
                </template>
            </div>
            <!-- Add/Edit Forms -->
            <div x-show="modalType === 'add' || modalType === 'edit'">
                <template x-if="modalForm === 'allergies'">
                    <livewire:patient.record.forms.allergy-form :patient="$patient" :record="null" :key="'allergy-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'family-history'">
                    <livewire:patient.record.forms.family-history-form :patient="$patient" :record="null"
                        :key="'family-history-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'diagnostic-test'">
                    <livewire:patient.record.forms.diagnostic-test-form :patient="$patient" :record="null"
                        :key="'diagnostic-test-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'immunization'">
                    <livewire:patient.record.forms.immunization-form :patient="$patient" :record="null"
                        :key="'immunization-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'medical-condition'">
                    <livewire:patient.record.forms.medical-condition-form :patient="$patient" :record="null"
                        :key="'medical-condition-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'medications'">
                    <livewire:patient.record.forms.medication-form :patient="$patient" :record="null"
                        :encounter="$this->encounter" :key="'medication-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'vital'">
                    <livewire:patient.record.forms.vital-sign-form :patient="$patient" :record="null"
                        :key="'vital-sign-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="modalForm === 'encounter'">
                    <livewire:patient.record.forms.encounter-form :patient="$patient" :record="null"
                        :key="'encounter-form-' . $patient->id . '-' . $modalType" />
                </template>
            </div>
        </div>
    </div>
</div>
