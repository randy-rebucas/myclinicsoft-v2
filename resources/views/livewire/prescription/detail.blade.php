<?php

use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Encounter;
use App\Enums\PrescriptionStatusEnum;
use Carbon\Carbon;
use function Livewire\Volt\{state, layout, mount, title};


state([
    'prescription' => null,
    'patient' => null,
    'doctor' => null,
    'encounter' => null,
]);

layout('layouts.app');

title(fn() => $this->prescription ? 'Prescription Details - ' . $this->prescription->medication_name : 'Prescription Not Found');

mount(function () {
    // Get the prescription ID from the URL parameter
    $prescriptionId = request()->route('prescription');
    
    // Handle both scalar ID and model instance from route model binding
    if (is_object($prescriptionId) && $prescriptionId instanceof Prescription) {
        $this->prescription = $prescriptionId;
    } else {
        $this->prescription = Prescription::with(['patient', 'doctor', 'encounter'])->find($prescriptionId);
    }
    
    if (!$this->prescription) {
        abort(404, 'Prescription not found');
    }
    
    $this->patient = $this->prescription->patient;
    $this->doctor = $this->prescription->doctor;
    $this->encounter = $this->prescription->encounter;
});

$getStatusColor = fn($status) => match ($status) {
    'active' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
};

$markAsReady = function () {
    if ($this->prescription) {
        $this->prescription->update(['status' => 'completed']);
        session()->flash('message', 'Prescription marked as ready.');
    }
};

?>

<section class="min-h-screen bg-gray-50/30 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Prescription Details</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View and manage prescription information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('prescriptions.edit', $prescription) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('prescriptions.print', $prescription) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </a>
                </div>
            </div>

            <!-- Status Alert -->
            @if($prescription->status === 'active')
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Active Prescription
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>This prescription is currently active. You can mark it as ready for pickup or print it for the patient.</p>
                            </div>
                            <div class="mt-4">
                                <button type="button" 
                                        wire:click="markAsReady"
                                        class="bg-yellow-100 dark:bg-yellow-800 px-3 py-2 rounded-md text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-700">
                                    Mark as Ready
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Prescription Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Prescription Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Medication Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $prescription->medication_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $this->getStatusColor($prescription->status) }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dosage</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $prescription->dosage }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Frequency</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $prescription->frequency }}</dd>
                        </div>
                        @if($prescription->quantity)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $prescription->quantity }}</dd>
                            </div>
                        @endif
                        @if($prescription->refills)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Refills</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $prescription->refills }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Date</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ Carbon::parse($prescription->start_date)->format('M d, Y') }}</dd>
                        </div>
                        @if($prescription->end_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ Carbon::parse($prescription->end_date)->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                    
                    @if($prescription->instructions)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Instructions</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $prescription->instructions }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Patient Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Patient Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Patient ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $patient->patient_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ Carbon::parse($patient->date_of_birth)->format('M d, Y') }} 
                                ({{ $patient->age }} years old)
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($patient->gender) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Doctor Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Prescribing Doctor</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Doctor Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Specialization</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $doctor->specialization ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Encounter Information -->
            @if($encounter)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Related Encounter</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Encounter Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ Carbon::parse($encounter->encounter_date)->format('M d, Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Chief Complaint</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $encounter->chief_complaint }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Timestamps -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Record Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ Carbon::parse($prescription->created_at)->format('M d, Y g:i A') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ Carbon::parse($prescription->updated_at)->format('M d, Y g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>
