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
use function Livewire\Volt\{state, mount};

state([
    'patient',
    'allergies' => [],
    'familyHistories' => [],
    'diagnosticTests' => [],
    'immunizations' => [],
    'medicalConditions' => [],
    'medications' => [],
    'physicalExaminations' => [],
    'vitalSigns' => [],
]);

mount(function () {
    $this->allergies = Allergy::where('patient_id', $this->patient->id)->get();
    $this->familyHistories = FamilyHistory::where('patient_id', $this->patient->id)->get();
    $this->diagnosticTests = DiagnosticTest::where('patient_id', $this->patient->id)->get();
    $this->immunizations = Immunization::where('patient_id', $this->patient->id)->get();
    $this->medicalConditions = MedicalCondition::where('patient_id', $this->patient->id)->get();
    $this->medications = Medication::where('patient_id', $this->patient->id)->get();
    $this->physicalExaminations = PhysicalExamination::where('patient_id', $this->patient->id)->get();
    $this->vitalSigns = Vital::where('patient_id', $this->patient->id)->get();
});
?>
<div x-data="{
    activeTab: 'allergies',
    showModal: false,
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
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
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
                            'physical-examinations' => 'Physical Examinations',
                            'vital-signs' => 'Vital Signs',
                        ];
                    @endphp

                    @foreach($navItems as $id => $label)
                        <a href="javascript:void(0)"
                           @click="activeTab = '{{ $id }}'"
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
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Allergies</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Allergies'"
                                class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Allergies'"
                                class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->allergies as $allergy)
                        <li>{{ $allergy->allergen }}</li>
                    @empty
                        <li>No allergies recorded.</li>
                    @endforelse
                </ul>
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

            <!-- Physical Examinations -->
            <div x-show="activeTab === 'physical-examinations'" class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-sm font-semibold text-gray-900">Physical Examinations</h3>
                    <div class="flex space-x-2">
                        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New Physical Examinations'"
                                class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-plus class="w-4 h-4" />
                        </button>
                        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View Physical Examinations'"
                                class="text-gray-500 hover:text-gray-700">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <ul class="list-disc pl-5 text-gray-700">
                    @forelse($this->physicalExaminations as $examination)
                        <li>{{ $examination->date }} - {{ $examination->findings }}</li>
                    @empty
                        <li>No physical examinations recorded.</li>
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
    <div x-show="showModal"
         class="fixed inset-0 bg-black bg-opacity-50 z-40"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Sliding Modal -->
    <div x-show="showModal"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-50">

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
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Allergen</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Reaction</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Allergy
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'family-history'">
                    <!-- Family History Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Condition</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Family History
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'diagnostic-tests'">
                    <!-- Diagnostic Tests Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Test Name</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Result</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Diagnostic Test
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'immunizations'">
                    <!-- Immunizations Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Vaccine</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Immunization
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'medical-conditions'">
                    <!-- Medical Conditions Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Condition Name</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Diagnosis Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Medical Condition
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'medications'">
                    <!-- Medications Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Medication Name</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Dosage</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Medication
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'physical-examinations'">
                    <!-- Physical Examinations Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Findings</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Examination
                        </button>
                    </div>
                </template>

                <template x-if="activeTab === 'vital-signs'">
                    <!-- Vital Signs Form -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Blood Pressure</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Heart Rate</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Temperature</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                            Save Vital Signs
                        </button>
                    </div>
                </template>
            </div>

            <!-- View Details -->
            <div x-show="modalType === 'view'">
                <template x-if="activeTab === 'allergies'">
                    <!-- Allergies Details -->
                    <div>
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Allergen</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Allergen</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Reaction</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Reaction</dd>
                            </div>
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
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Date</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Blood Pressure</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample BP</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Heart Rate</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample HR</dd>
                            </div>
                            <div class="py-4">
                                <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                                <dd class="mt-1 text-sm text-gray-900">Sample Temp</dd>
                            </div>
                        </dl>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Update the buttons in each tab section -->
    <div class="flex space-x-2">
        <button @click="showModal = true; modalType = 'add'; modalTitle = 'Add New ' + '{{ $label }}'"
                class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-plus class="w-4 h-4" />
        </button>
        <button @click="showModal = true; modalType = 'view'; modalTitle = 'View ' + '{{ $label }}'"
                class="text-gray-500 hover:text-gray-700">
            <x-heroicon-o-eye class="w-4 h-4" />
        </button>
    </div>
</div>
