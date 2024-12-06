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
use function Livewire\Volt\{state, mount, on, computed};

state(['patient', 'showModal' => false, 'activeTab' => 'allergies']);

mount(function () {});

$allergies = computed(function () {
    return Allergy::where('patient_id', $this->patient->id)->get();
});

$familyHistories = computed(function () {
    return FamilyHistory::where('patient_id', $this->patient->id)->get();
});

$diagnosticTests = computed(function () {
    return DiagnosticTest::where('patient_id', $this->patient->id)->get();
});

$immunizations = computed(function () {
    return Immunization::where('patient_id', $this->patient->id)->get();
});

$medicalConditions = computed(function () {
    return MedicalCondition::where('patient_id', $this->patient->id)->get();
});

$medications = computed(function () {
    return Medication::where('patient_id', $this->patient->id)->get();
});

$vitalSigns = computed(function () {
    return Vital::where('patient_id', $this->patient->id)->get();
});

on([
    'close-modal' => function ($record_type = null) {
        $this->showModal = false;
        $this->dispatch('refresh');
        if ($record_type) {
            $this->activeTab = $record_type;
        }
    },
]);
?>
<div x-data="{
    activeTab: @entangle('activeTab'),
    showModal: @entangle('showModal'),
    modalType: null,
    modalTitle: ''
}" class="w-full mx-auto p-8">
    <!-- Patient Information Section - Moved to top -->
    <div id="patient-info" class="bg-white shadow-lg rounded-lg mb-8">
        <div class="flex items-start gap-4 px-4 py-4">
            <!-- Avatar Column -->
            <div class="flex-shrink-0">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100">
                    @if ($patient->avatar)
                        <img src="{{ $patient->avatar }}" alt="{{ $patient->full_name }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Patient Details -->
            <div class="flex-grow grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @php
                    $details = [
                        'Full Name' => $patient->full_name,
                        'DOB' => $patient->date_of_birth,
                        'Gender' => $patient->gender,
                        'Blood Type' => $patient->blood_type,
                        'Height' => $patient->height . ' cm',
                        'Weight' => $patient->weight . ' kg',
                        'Contact' => $patient->phone_number,
                        'Email' => $patient->user->email,
                    ];
                @endphp

                @foreach ($details as $label => $value)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex gap-8">
        <!-- Left Tab Navigation -->
        <div class="w-64 flex-shrink-0">
            <div class="bg-white shadow-lg rounded-lg p-4">
                <nav class="space-y-2">
                    @php
                        $navItems = [
                            'allergies' => 'Allergies',
                            'family-history' => 'Family History',
                            'diagnostic-tests' => 'Diagnostic Tests',
                            'immunizations' => 'Immunizations',
                            'medical-conditions' => 'Medical Conditions',
                            'medications' => 'Medications',
                            'vital-signs' => 'Vital Signs',
                        ];
                    @endphp

                    @foreach ($navItems as $id => $label)
                        <a href="javascript:void(0)" @click="activeTab = '{{ $id }}'"
                            :class="{ 'bg-gray-50 text-gray-900 border-l-4 border-blue-500': activeTab === '{{ $id }}' }"
                            class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 mr-3 text-gray-400" />
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Tab Content -->
            <div x-show="activeTab === 'allergies'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Allergies</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Allergies'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->allergies as $allergy)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $allergy->allergen }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Reaction:</span>
                                            <span class="text-gray-900">{{ $allergy->reaction }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Severity:</span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $allergy->severity === 'High'
                                                    ? 'bg-red-100 text-red-800'
                                                    : ($allergy->severity === 'Medium'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-green-100 text-green-800') }}">
                                                {{ $allergy->severity }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; modalTitle = 'Edit Allergies'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Allergies</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new allergy record.</p>
                            <div class="mt-6">
                                <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Allergies'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Allergy
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div x-show="activeTab === 'family-history'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Family History</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Family History'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Family History'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->familyHistories as $history)
                        <li>{{ $history->condition }}</li>
                    @empty
                        <li>No family history recorded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Example for diagnostic-tests -->
            <div x-show="activeTab === 'diagnostic-tests'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Diagnostic Tests</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Diagnostic Tests'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Diagnostic Tests'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->diagnosticTests as $test)
                        <li>{{ $test->name }} - {{ $test->result }}</li>
                    @empty
                        <li>No diagnostic tests recorded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Immunizations -->
            <div x-show="activeTab === 'immunizations'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Immunizations</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Immunizations'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Immunizations'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->immunizations as $immunization)
                        <li>{{ $immunization->vaccine }} - {{ $immunization->date }}</li>
                    @empty
                        <li>No immunizations recorded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Medical Conditions -->
            <div x-show="activeTab === 'medical-conditions'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Medical Conditions</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Conditions'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Medical Conditions'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->medicalConditions as $condition)
                        <li>{{ $condition->name }} - {{ $condition->diagnosis_date }}</li>
                    @empty
                        <li>No medical conditions recorded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Medications -->
            <div x-show="activeTab === 'medications'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Medications</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medications'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Medications'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->medications as $medication)
                        <li>{{ $medication->name }} - {{ $medication->dosage }}</li>
                    @empty
                        <li>No medications recorded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Vital Signs -->
            <div x-show="activeTab === 'vital-signs'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Vital Signs</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Vital Signs'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Vital Signs'"
                            class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->vitalSigns as $vital)
                        <li>
                            {{ $vital->date }} -
                            BP: {{ $vital->blood_pressure }},
                            HR: {{ $vital->heart_rate }},
                            Temp: {{ $vital->temperature }}°C
                        </li>
                    @empty
                        <li>No vital signs recorded.</li>
                    @endforelse
                </ul>
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
            <!-- Add Form -->
            <div x-show="modalType === 'add'">
                <template x-if="activeTab === 'allergies'">
                    <!-- Allergies Form -->
                    <livewire:patient.record.allergy :patient="$patient" />
                </template>

                <template x-if="activeTab === 'family-history'">
                    <!-- Family History Form -->
                    <livewire:patient.record.family-history :patient="$patient" />
                </template>

                <template x-if="activeTab === 'diagnostic-tests'">
                    <!-- Diagnostic Tests Form -->
                    <livewire:patient.record.diagnostic-test :patient="$patient" />
                </template>

                <template x-if="activeTab === 'immunizations'">
                    <!-- Immunizations Form -->
                    <livewire:patient.record.immunization :patient="$patient" />
                </template>

                <template x-if="activeTab === 'medical-conditions'">
                    <!-- Medical Conditions Form -->
                    <livewire:patient.record.medical-condition :patient="$patient" />
                </template>

                <template x-if="activeTab === 'medications'">
                    <!-- Medications Form -->
                    <livewire:patient.record.medication :patient="$patient" />
                </template>

                <template x-if="activeTab === 'vital-signs'">
                    <!-- Vital Signs Form -->
                    <livewire:patient.record.vital-sign :patient="$patient" />
                </template>
            </div>

            <!-- View Details -->
            <div x-show="modalType === 'view'">
                <template x-if="activeTab === 'allergies'">
                    <!-- Allergies Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            @foreach ($this->allergies as $allergy)
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Allergen</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $allergy->allergen }}</dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Reaction</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $allergy->reaction }}</dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Severity</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $allergy->severity }}</dd>
                                </div>
                                <div class="border-t border-gray-200"></div>
                            @endforeach
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'family-history'">
                    <!-- Family History Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Condition</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Condition</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'diagnostic-tests'">
                    <!-- Diagnostic Tests Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Test Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Test</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Result</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Result</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'immunizations'">
                    <!-- Immunizations Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Vaccine</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Vaccine</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Date</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'medical-conditions'">
                    <!-- Medical Conditions Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Condition Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Condition</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Diagnosis Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Date</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'medications'">
                    <!-- Medications Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Medication Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Medication</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Dosage</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Dosage</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'physical-examinations'">
                    <!-- Physical Examinations Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Date</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Findings</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Findings</dd>
                            </div>
                        </dl>
                    </div>
                </template>

                <template x-if="activeTab === 'vital-signs'">
                    <!-- Vital Signs Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            @foreach ($this->vitalSigns as $vital)
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vital->date }}</dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Blood Pressure</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vital->blood_pressure }}</dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Heart Rate</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vital->heart_rate }} bpm</dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vital->temperature }}°C</dd>
                                </div>
                                <div class="border-t border-gray-200"></div>
                            @endforeach
                        </dl>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
