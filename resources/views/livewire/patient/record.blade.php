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

state(['patient', 'showModal' => false, 'activeTab' => 'allergies', 'modalType' => null, 'selectedRecord' => null]);

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

        if ($record_type) {
            $this->activeTab = $record_type;
        }

        $this->dispatch('refresh');
    },
]);
?>
<div x-data="{
    activeTab: @entangle('activeTab'),
    showModal: @entangle('showModal'),
    modalType: @entangle('modalType'),
    selectedRecord: @entangle('selectedRecord'),
    modalTitle: ''
}" @close-modal.window="showModal = false" class="w-full mx-auto p-8">
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
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Family History</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Family History'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->familyHistories as $history)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $history->condition }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Relation:</span>
                                            <span class="text-gray-900">{{ $history->relationship }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Notes:</span>
                                            <span class="text-gray-900">{{ $history->notes }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $history->id }}; modalTitle = 'Edit Family History'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Family History</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new family history record.
                            </p>
                            <div class="mt-6">
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Family History'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Family History
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Diagnostic Tests -->
            <div x-show="activeTab === 'diagnostic-tests'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Diagnostic Tests</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Diagnostic Test'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->diagnosticTests as $test)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $test->test_name }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Result:</span>
                                            <span class="text-gray-900">{{ $test->results }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Date:</span>
                                            <span class="text-gray-900">{{ $test->test_date }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Notes:</span>
                                            <span class="text-gray-900">{{ $test->notes }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $test->id }}; modalTitle = 'Edit Diagnostic Test'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Diagnostic Tests</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new diagnostic test record.
                            </p>
                            <div class="mt-6">
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Diagnostic Test'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Diagnostic Test
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Immunizations -->
            <div x-show="activeTab === 'immunizations'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Immunizations</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Immunization'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->immunizations as $immunization)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $immunization->vaccine }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Date:</span>
                                            <span class="text-gray-900">{{ $immunization->date }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Provider:</span>
                                            <span class="text-gray-900">{{ $immunization->provider }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Notes:</span>
                                            <span class="text-gray-900">{{ $immunization->notes }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $immunization->id }}; modalTitle = 'Edit Immunization'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Immunizations</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new immunization record.
                            </p>
                            <div class="mt-6">
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Immunization'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Immunization
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Medical Conditions -->
            <div x-show="activeTab === 'medical-conditions'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Medical Conditions</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Condition'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->medicalConditions as $condition)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $condition->name }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Status:</span>
                                            <span class="text-gray-900">{{ $condition->status }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Diagnosed:</span>
                                            <span class="text-gray-900">{{ $condition->diagnosis_date }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Notes:</span>
                                            <span class="text-gray-900">{{ $condition->notes }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $condition->id }}; modalTitle = 'Edit Medical Condition'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Medical Conditions</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new medical condition
                                record.</p>
                            <div class="mt-6">
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medical Condition'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Medical Condition
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Medications -->
            <div x-show="activeTab === 'medications'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Medications</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medication'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->medications as $medication)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $medication->name }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Dosage:</span>
                                            <span class="text-gray-900">{{ $medication->dosage }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Frequency:</span>
                                            <span class="text-gray-900">{{ $medication->frequency }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-20">Start Date:</span>
                                            <span class="text-gray-900">{{ $medication->start_date }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $medication->id }}; modalTitle = 'Edit Medication'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Medications</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new medication record.</p>
                            <div class="mt-6">
                                <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Medication'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Medication
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Vital Signs -->
            <div x-show="activeTab === 'vital-signs'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vital Signs</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Vital Signs'"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Add New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($this->vitalSigns as $vital)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $vital->date }}</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-32">Blood Pressure:</span>
                                            <span class="text-gray-900">{{ $vital->blood_pressure }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-32">Heart Rate:</span>
                                            <span class="text-gray-900">{{ $vital->heart_rate }} bpm</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-32">Temperature:</span>
                                            <span class="text-gray-900">{{ $vital->temperature }}°C</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <span class="font-medium text-gray-500 w-32">Respiratory Rate:</span>
                                            <span class="text-gray-900">{{ $vital->respiratory_rate }} /min</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button @click="showModal = true; modalType = 'edit'; selectedRecord = {{ $vital->id }}; modalTitle = 'Edit Vital Signs'"
                                        class="text-gray-400 hover:text-gray-500">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8">
                            <x-heroicon-o-exclamation-circle class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Vital Signs</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new vital signs record.</p>
                            <div class="mt-6">
                                <button
                                    @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Vital Signs'"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                                    Add Vital Signs
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
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
            <!-- Add/Edit Forms -->
            <div x-show="modalType === 'add' || modalType === 'edit'">
                <template x-if="activeTab === 'allergies'">
                    <livewire:patient.record.forms.allergy-form :patient="$patient" :record="null" :key="'allergy-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'family-history'">
                    <livewire:patient.record.forms.family-history-form :patient="$patient" :record="null"
                        :key="'family-history-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'diagnostic-tests'">
                    <livewire:patient.record.forms.diagnostic-test-form :patient="$patient" :record="null"
                        :key="'diagnostic-test-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'immunizations'">
                    <livewire:patient.record.forms.immunization-form :patient="$patient" :record="null"
                        :key="'immunization-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'medical-conditions'">
                    <livewire:patient.record.forms.medical-condition-form :patient="$patient" :record="null"
                        :key="'medical-condition-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'medications'">
                    <livewire:patient.record.forms.medication-form :patient="$patient" :record="null"
                        :key="'medication-form-' . $patient->id . '-' . $modalType" />
                </template>

                <template x-if="activeTab === 'vital-signs'">
                    <livewire:patient.record.forms.vital-sign-form :patient="$patient" :record="null"
                        :key="'vital-sign-form-' . $patient->id . '-' . $modalType" />
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
