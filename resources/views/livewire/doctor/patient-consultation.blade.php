<?php

use App\Models\Queue;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Vital;
use App\Models\Allergy;
use App\Models\Medication;
use App\Models\Prescription;
use Carbon\Carbon;
use function Livewire\Volt\{state, mount, computed};

state([
    'queue' => null,
    'patient' => null,
    'encounter' => null,
    'patientHistory' => [],
    'currentVitals' => null,
    'allergies' => [],
    'currentMedications' => [],
]);

mount(function (Queue $queue) {
    $this->queue = $queue;
    $this->patient = $queue->patient;
    
    // Get or create today's encounter
    $this->encounter = $this->patient->encounters()
        ->whereDate('encounter_date', Carbon::today())
        ->where('doctor_id', auth()->user()->doctor->id)
        ->first();
    
    if (!$this->encounter) {
        $this->encounter = Encounter::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => auth()->user()->doctor->id,
            'encounter_date' => Carbon::today(),
            'encounter_time' => now(),
            'status' => 'in_progress',
            'chief_complaint' => '',
            'diagnosis' => '',
            'treatment_plan' => '',
            'notes' => '',
        ]);
    }
    
    // Load patient data
    $this->loadPatientData();
});

$loadPatientData = function () {
    // Get patient history (last 5 encounters)
    $this->patientHistory = $this->patient->encounters()
        ->with('doctor')
        ->where('id', '!=', $this->encounter->id)
        ->orderBy('encounter_date', 'desc')
        ->take(5)
        ->get();
    
    // Get current vitals (most recent)
    $this->currentVitals = $this->patient->vitals()
        ->orderBy('created_at', 'desc')
        ->first();
    
    // Get allergies
    $this->allergies = $this->patient->allergies ?? collect();
    
    // Get current medications (from prescriptions)
    $this->currentMedications = $this->patient->prescriptions()
        ->where('status', 'active')
        ->get();
};

$startDiagnosis = function () {
    $this->dispatch('start-diagnosis');
};

$startPrescription = function () {
    $this->dispatch('start-prescription');
};

$startFollowup = function () {
    $this->dispatch('start-followup');
};

$completeConsultation = function () {
    $this->encounter->update([
        'status' => 'completed',
    ]);
    
    $this->queue->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    
    $this->dispatch('queue-completed');
};

?>

<div class="space-y-6">
    <!-- Patient Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-blue-600">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $patient->full_name }}</h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span>ID: {{ $patient->id }}</span>
                        @if($patient->date_of_birth)
                            <span>{{ $patient->date_of_birth->age }} years old</span>
                        @endif
                        <span>{{ $patient->phone }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="startDiagnosis" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Enter Diagnosis
                </button>
                <button wire:click="startPrescription" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Generate Prescription
                </button>
                <button wire:click="startFollowup" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Schedule Follow-up
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Patient Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Current Encounter -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Consultation</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint</label>
                        <textarea wire:model.live="encounter.chief_complaint" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="3"
                                  placeholder="Enter the patient's chief complaint..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea wire:model.live="encounter.notes" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="4"
                                  placeholder="Add consultation notes..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Patient History -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Medical History</h3>
                <div class="space-y-4">
                    @forelse($patientHistory as $history)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">
                                    {{ $history->encounter_date->format('M d, Y') }}
                                </h4>
                                <span class="text-sm text-gray-500">
                                    Dr. {{ $history->doctor->user->name }}
                                </span>
                            </div>
                            @if($history->chief_complaint)
                                <p class="text-sm text-gray-600 mb-2">
                                    <span class="font-medium">Complaint:</span> {{ $history->chief_complaint }}
                                </p>
                            @endif
                            @if($history->diagnosis)
                                <p class="text-sm text-gray-600 mb-2">
                                    <span class="font-medium">Diagnosis:</span> {{ $history->diagnosis }}
                                </p>
                            @endif
                            @if($history->treatment_plan)
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Treatment:</span> {{ $history->treatment_plan }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No previous medical history found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Info -->
        <div class="space-y-6">
            <!-- Current Vitals -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Vitals</h3>
                @if($currentVitals)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Blood Pressure</p>
                            <p class="font-semibold">{{ $currentVitals->systolic }}/{{ $currentVitals->diastolic }} mmHg</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Heart Rate</p>
                            <p class="font-semibold">{{ $currentVitals->heart_rate }} bpm</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Temperature</p>
                            <p class="font-semibold">{{ $currentVitals->temperature }}°F</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Weight</p>
                            <p class="font-semibold">{{ $currentVitals->weight }} kg</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Recorded {{ $currentVitals->created_at->diffForHumans() }}
                    </p>
                @else
                    <p class="text-gray-500 text-center py-4">No vitals recorded yet.</p>
                @endif
            </div>

            <!-- Allergies -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Allergies</h3>
                @if($allergies && $allergies->count() > 0)
                    <div class="space-y-2">
                        @foreach($allergies as $allergy)
                            <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                                <span class="text-sm font-medium text-red-800">{{ $allergy->allergen }}</span>
                                <span class="text-xs text-red-600">{{ $allergy->severity }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No known allergies.</p>
                @endif
            </div>

            <!-- Current Medications -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Medications</h3>
                @if($currentMedications && $currentMedications->count() > 0)
                    <div class="space-y-2">
                        @foreach($currentMedications as $medication)
                            <div class="p-2 bg-blue-50 rounded">
                                <p class="text-sm font-medium text-blue-800">{{ $medication->medication_name }}</p>
                                <p class="text-xs text-blue-600">{{ $medication->dosage }} - {{ $medication->frequency }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No current medications.</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button wire:click="startDiagnosis" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Enter Diagnosis
                    </button>
                    <button wire:click="startPrescription" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Generate Prescription
                    </button>
                    <button wire:click="startFollowup" 
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Schedule Follow-up
                    </button>
                    <button wire:click="completeConsultation" 
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Complete Consultation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
