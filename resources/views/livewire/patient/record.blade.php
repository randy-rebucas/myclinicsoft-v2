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
use function Livewire\Volt\{state, mount, computed};

state([
    'patient',
    'showModal' => false,
    'modalType' => null,
    'selectedRecord' => null,
]);

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

$encounters = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->with('doctor')
        ->latest()
        ->get() ?? collect();
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

?>

<div class="w-full mx-auto p-8">
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
                    <p class="font-medium">{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</p>
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
                        <button @click="$dispatch('show-modal', { type: 'encounter' })"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            New Encounter
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach($this->encounters as $encounter)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ $encounter->encounter_date ? $encounter->encounter_date->format('M d, Y') : 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">Dr. {{ $encounter->doctor->name }}</p>
                                    </div>
                                    <button @click="$dispatch('show-encounter-details', {{ $encounter->id }})"
                                        class="text-blue-600 hover:text-blue-700 text-sm">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Medication Section -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Current Medications</h3>
                        <button @click="$dispatch('show-modal', { type: 'medication' })"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add Medication
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach($this->medications as $medication)
                            <div class="border rounded-lg p-4">
                                @foreach($medication->prescription_items as $item)
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-medium">{{ $item['medication_name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $item['dosage'] }} - {{ $item['frequency'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
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
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Allergies ({{ $this->allergies->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->allergies as $allergy)
                                    <div class="text-sm">
                                        <p class="font-medium">{{ $allergy->allergen }}</p>
                                        <p class="text-gray-600">{{ $allergy->reaction }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'allergy' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Allergy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Family History -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Family History ({{ $this->familyHistories->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->familyHistories as $history)
                                    <div class="text-sm">
                                        <p class="font-medium">{{ $history->condition }}</p>
                                        <p class="text-gray-600">{{ $history->relation }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'family-history' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Family History
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnostic Tests -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Diagnostic Tests ({{ $this->diagnosticTests->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->diagnosticTests as $test)
                                    <div class="text-sm">
                                        <p class="font-medium">{{ $test->test_name }}</p>
                                        <p class="text-gray-600">{{ $test->date ? $test->date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'diagnostic-test' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Diagnostic Test
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Immunizations -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Immunizations ({{ $this->immunizations->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->immunizations as $immunization)
                                    <div class="text-sm">
                                        <p class="font-medium">{{ $immunization->vaccine_name }}</p>
                                        <p class="text-gray-600">{{ $immunization->date ? $immunization->date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'immunization' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Immunization
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Conditions -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Medical Conditions ({{ $this->medicalConditions->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->medicalConditions as $condition)
                                    <div class="text-sm">
                                        <p class="font-medium">{{ $condition->condition_name }}</p>
                                        <p class="text-gray-600">{{ $condition->diagnosis_date ? $condition->diagnosis_date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'medical-condition' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Medical Condition
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Physical Examinations -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Physical Examinations ({{ $this->physicalExaminations->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->physicalExaminations as $exam)
                                    <div class="text-sm">
                                        <p class="font-medium">Physical Examination</p>
                                        <p class="text-gray-600">{{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'physical-examination' })"
                                    class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                                    + Add Physical Examination
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Vitals -->
                    <div x-data="{ open: false }" class="border rounded-md">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <span>Vitals ({{ $this->vitals->count() }})</span>
                            <span class="transform transition-transform duration-200" :class="{'rotate-180': open}">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open" class="px-4 py-2 bg-gray-50">
                            <div class="space-y-2">
                                @foreach($this->vitals as $vital)
                                    <div class="text-sm">
                                        <p class="font-medium">Vitals Check</p>
                                        <p class="text-gray-600">{{ $vital->recorded_at ? $vital->recorded_at->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                @endforeach
                                <button @click="$dispatch('show-modal', { type: 'vital' })"
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

    <!-- Modal Component -->
    <div x-show="showModal"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative bg-white rounded-lg max-w-xl w-full">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
                <div class="p-6">
                    <!-- Modal content will be loaded here -->
                    @switch($modalType)
                        @case('medication')
                            <livewire:patient.record.forms.medication-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('allergy')
                            <livewire:patient.record.forms.allergy-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('family-history')
                            <livewire:patient.record.forms.family-history-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('diagnostic-test')
                            <livewire:patient.record.forms.diagnostic-test-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('immunization')
                            <livewire:patient.record.forms.immunization-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('medical-condition')
                            <livewire:patient.record.forms.medical-condition-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('physical-examination')
                            <livewire:patient.record.forms.physical-examination-form :patient="$patient" :record="$selectedRecord" />
                            @break
                        @case('vital')
                            <livewire:patient.record.forms.vital-form :patient="$patient" :record="$selectedRecord" />
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
