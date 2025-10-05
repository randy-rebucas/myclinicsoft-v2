<?php

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\Vital;
use App\Enums\AppointmentStatusEnum;
use function Livewire\Volt\{layout, state, mount, computed, on, title};

state(['encounterId'])->url();

state([
    'encounter' => null,
    'patient' => null,
    'prescriptions' => [],
    'medications' => [],
    'vitals' => [],
    'showEditModal' => false,
    'showPrescriptionModal' => false,
    'showVitalModal' => false,
    'showMedicationModal' => false,
    'activeTab' => 'overview',
    'status' => 'scheduled',
    'statusReason' => '',
    'chief_complaint' => '',
    'diagnosis' => '',
    'treatment_plan' => '',
    'follow_up_date' => '',
    'notes' => '',
]);

layout('layouts.app');

title(fn() => $this->encounter && $this->patient ? 'Encounter Details - ' . $this->patient->full_name : 'Encounter Not Found');

mount(function () {
    // Handle both scalar ID and model instance from route model binding
    if (is_object($this->encounterId) && $this->encounterId instanceof Encounter) {
        $this->encounter = $this->encounterId;
    } else {
        $this->encounter = Encounter::with(['patient', 'doctor', 'prescriptions', 'medications'])
            ->find($this->encounterId);
    }
    
    if (!$this->encounter) {
        abort(404, 'Encounter not found');
    }
    
    $this->patient = $this->encounter->patient;
    $this->status = $this->encounter->status ?? 'scheduled';
    
    // Load related data
    $this->loadRelatedData();
    
    // Initialize edit form fields
    $this->initializeEditFields();
});

$loadRelatedData = function () {
    $this->prescriptions = $this->encounter->prescriptions()
        ->with(['patient', 'doctor'])
        ->orderBy('created_at', 'desc')
        ->get();

    $this->medications = $this->encounter->medications()
        ->orderBy('created_at', 'desc')
        ->get();

    $this->vitals = $this->patient->vitals()
        ->whereDate('created_at', $this->encounter->encounter_date)
        ->orderBy('created_at', 'desc')
        ->get();
};

$initializeEditFields = function () {
    $this->chief_complaint = $this->encounter->chief_complaint;
    $this->diagnosis = $this->encounter->diagnosis;
    $this->treatment_plan = $this->encounter->treatment_plan;
    $this->follow_up_date = $this->encounter->follow_up_date?->format('Y-m-d');
    $this->notes = $this->encounter->notes;
};

$startEncounter = function () {
    $this->status = 'in_progress';
    $this->updateEncounterStatus();
    
    session()->flash('message', 'Encounter started successfully.');
    $this->dispatch('encounter-started');
};

$completeEncounter = function () {
    $this->status = 'completed';
    $this->updateEncounterStatus();
    
    session()->flash('message', 'Encounter completed successfully.');
    $this->dispatch('encounter-completed');
};

$cancelEncounter = function () {
    $this->validate([
        'statusReason' => 'required|string|max:255'
    ], [
        'statusReason.required' => 'Reason is required when cancelling an encounter.',
        'statusReason.max' => 'Reason cannot exceed 255 characters.',
    ]);

    $this->status = 'cancelled';
    $this->updateEncounterStatus();
    
    // Add cancellation reason to notes
    $currentNotes = $this->encounter->notes ?? '';
    $cancellationNote = "\n\n[CANCELLED] Reason: " . $this->statusReason . " - " . now()->format('M d, Y H:i');
    $this->encounter->update(['notes' => $currentNotes . $cancellationNote]);
    
    session()->flash('message', 'Encounter cancelled successfully.');
    $this->dispatch('encounter-cancelled');
};

$updateEncounterStatus = function () {
    $this->encounter->update([
        'status' => $this->status,
        'encounter_time' => $this->status === 'in_progress' ? now() : $this->encounter->encounter_time,
    ]);

    // Record activity
    $this->encounter->recordActivity('updated', "Encounter status changed to {$this->status}");
};

$updateEncounter = function () {
    $this->validate([
        'chief_complaint' => 'required|string|max:255',
        'diagnosis' => 'nullable|string|max:500',
        'treatment_plan' => 'nullable|string|max:1000',
        'follow_up_date' => 'nullable|date|after:today',
        'notes' => 'nullable|string|max:3000',
    ], [
        'chief_complaint.required' => 'Chief complaint is required.',
        'chief_complaint.max' => 'Chief complaint cannot exceed 255 characters.',
        'diagnosis.max' => 'Diagnosis cannot exceed 500 characters.',
        'treatment_plan.max' => 'Treatment plan cannot exceed 1000 characters.',
        'follow_up_date.after' => 'Follow-up date must be in the future.',
        'notes.max' => 'Notes cannot exceed 3000 characters.',
    ]);

    $this->encounter->update([
        'chief_complaint' => $this->chief_complaint,
        'diagnosis' => $this->diagnosis,
        'treatment_plan' => $this->treatment_plan,
        'follow_up_date' => $this->follow_up_date,
        'notes' => $this->notes,
    ]);

    // Record activity
    $this->encounter->recordActivity('updated', 'Encounter details updated');

    $this->showEditModal = false;
    session()->flash('message', 'Encounter updated successfully.');
    $this->dispatch('encounter-updated');
};

$deleteEncounter = function () {
    // Check if encounter can be deleted (only if not completed or cancelled)
    if (in_array($this->encounter->status, ['completed', 'cancelled'])) {
        session()->flash('error', 'Cannot delete completed or cancelled encounters.');
        return;
    }

    // Check if encounter has related prescriptions or medications
    if ($this->encounter->prescriptions()->count() > 0 || $this->encounter->medications()->count() > 0) {
        session()->flash('error', 'Cannot delete encounter with existing prescriptions or medications.');
        return;
    }

    $this->encounter->delete();
    
    session()->flash('message', 'Encounter deleted successfully.');
    return redirect()->route('encounters.index');
};

$openEditModal = function () {
    $this->initializeEditFields();
    $this->showEditModal = true;
};

$closeEditModal = function () {
    $this->showEditModal = false;
    $this->resetValidation();
};

$openPrescriptionModal = function () {
    $this->showPrescriptionModal = true;
};

$openVitalModal = function () {
    $this->showVitalModal = true;
};

$openMedicationModal = function () {
    $this->showMedicationModal = true;
};

$refreshData = function () {
    $this->encounter->refresh();
    $this->loadRelatedData();
};

$getStatusBadgeClass = function () {
    return match ($this->encounter->status) {
        'scheduled' => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
};

$getStatusLabel = function () {
    return match ($this->encounter->status) {
        'scheduled' => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        default => 'Unknown',
    };
};

$canEdit = function () {
    return !in_array($this->encounter->status, ['completed', 'cancelled']);
};

$canStart = function () {
    return $this->encounter->status === 'scheduled';
};

$canComplete = function () {
    return $this->encounter->status === 'in_progress';
};

$canCancel = function () {
    return in_array($this->encounter->status, ['scheduled', 'in_progress']);
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('encounters.index') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Encounter Details</h1>
                        <p class="text-sm text-gray-600">{{ $patient->full_name ?? 'Unknown Patient' }}</p>
                    </div>
                </div>
                
                <!-- Status Badge -->
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $getStatusBadgeClass() }}">
                        {{ $getStatusLabel() }}
                    </span>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        @if($canStart())
                            <button wire:click="startEncounter" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h1m4 0h1m6-8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Start
                            </button>
                        @endif
                        
                        @if($canComplete())
                            <button wire:click="completeEncounter" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Complete
                            </button>
                        @endif
                        
                        @if($canCancel())
                            <button wire:click="$set('showCancelModal', true)" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                        @endif
                        
                        @if($canEdit())
                            <button wire:click="openEditModal" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'prescriptions')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'prescriptions' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Prescriptions ({{ count($prescriptions) }})
                </button>
                <button wire:click="$set('activeTab', 'medications')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'medications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Medications ({{ count($medications) }})
                </button>
                <button wire:click="$set('activeTab', 'vitals')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'vitals' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Vitals ({{ count($vitals) }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            @if($activeTab === 'overview')
                <!-- Overview Tab -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Patient Information -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $patient->full_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Age</dt>
                                    <dd class="text-sm text-gray-900">{{ $patient->age ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($patient->gender ?? 'N/A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="text-sm text-gray-900">{{ $patient->phone_number ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Blood Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $patient->blood_type ?? 'N/A' }}</dd>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('patients.detail', $patient->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                    View Full Patient Profile →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Encounter Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Encounter Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Encounter Date</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->encounter_date?->format('M d, Y') ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Encounter Time</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->encounter_time?->format('h:i A') ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->doctor?->full_name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Follow-up Date</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->follow_up_date?->format('M d, Y') ?? 'N/A' }}</dd>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Chief Complaint</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->chief_complaint ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->diagnosis ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Treatment Plan</dt>
                                        <dd class="text-sm text-gray-900">{{ $encounter->treatment_plan ?? 'N/A' }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        @if($encounter->notes)
                            <div class="bg-white shadow rounded-lg p-6 mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                                <div class="prose max-w-none">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $encounter->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($activeTab === 'prescriptions')
                <!-- Prescriptions Tab -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Prescriptions</h3>
                            <button wire:click="openPrescriptionModal" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Prescription
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(count($prescriptions) > 0)
                            <div class="space-y-4">
                                @foreach($prescriptions as $prescription)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $prescription->medication_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $prescription->dosage }} - {{ $prescription->frequency }}</p>
                                                <p class="text-sm text-gray-500">Quantity: {{ $prescription->quantity }} | Refills: {{ $prescription->refills }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $prescription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($prescription->status) }}
                                            </span>
                                        </div>
                                        @if($prescription->instructions)
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-700">{{ $prescription->instructions }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No prescriptions</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding a new prescription.</p>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($activeTab === 'medications')
                <!-- Medications Tab -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Medications</h3>
                            <button wire:click="openMedicationModal" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Medication
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(count($medications) > 0)
                            <div class="space-y-4">
                                @foreach($medications as $medication)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">Medication Record</h4>
                                                <p class="text-sm text-gray-600">Created: {{ $medication->created_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        @if($medication->notes)
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-700">{{ $medication->notes }}</p>
                                            </div>
                                        @endif
                                        @if($medication->prescription_items)
                                            <div class="mt-3">
                                                <h5 class="text-sm font-medium text-gray-900 mb-2">Prescription Items:</h5>
                                                <div class="space-y-1">
                                                    @foreach($medication->prescription_items as $item)
                                                        <div class="text-sm text-gray-600">
                                                            • {{ $item['name'] ?? 'Unknown' }} - {{ $item['dosage'] ?? 'N/A' }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No medications</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding a new medication record.</p>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($activeTab === 'vitals')
                <!-- Vitals Tab -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Vital Signs</h3>
                            <button wire:click="openVitalModal" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Vital Signs
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(count($vitals) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($vitals as $vital)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-medium text-gray-900">Vital Signs</h4>
                                            <span class="text-xs text-gray-500">{{ $vital->created_at?->format('M d, Y H:i') ?? 'N/A' }}</span>
                                        </div>
                                        <div class="space-y-2">
                                            @if($vital->blood_pressure)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Blood Pressure:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->blood_pressure }}</span>
                                                </div>
                                            @endif
                                            @if($vital->heart_rate)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Heart Rate:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->heart_rate }} bpm</span>
                                                </div>
                                            @endif
                                            @if($vital->temperature)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Temperature:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->temperature }}°C</span>
                                                </div>
                                            @endif
                                            @if($vital->respiratory_rate)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Respiratory Rate:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->respiratory_rate }} /min</span>
                                                </div>
                                            @endif
                                            @if($vital->oxygen_saturation)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Oxygen Saturation:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->oxygen_saturation }}%</span>
                                                </div>
                                            @endif
                                            @if($vital->blood_sugar)
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-600">Blood Sugar:</span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $vital->blood_sugar }} mg/dL</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No vital signs</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding vital signs for this encounter.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Edit Encounter</h3>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="updateEncounter">
                        <div class="space-y-4">
                            <div>
                                <label for="chief_complaint" class="block text-sm font-medium text-gray-700">Chief Complaint *</label>
                                <input type="text" wire:model="chief_complaint" id="chief_complaint" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('chief_complaint') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                                <textarea wire:model="diagnosis" id="diagnosis" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('diagnosis') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="treatment_plan" class="block text-sm font-medium text-gray-700">Treatment Plan</label>
                                <textarea wire:model="treatment_plan" id="treatment_plan" rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('treatment_plan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="follow_up_date" class="block text-sm font-medium text-gray-700">Follow-up Date</label>
                                <input type="date" wire:model="follow_up_date" id="follow_up_date" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('follow_up_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea wire:model="notes" id="notes" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeEditModal" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Encounter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancel Modal -->
    @if($showCancelModal ?? false)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Cancel Encounter</h3>
                        <button wire:click="$set('showCancelModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="cancelEncounter">
                        <div class="mb-4">
                            <label for="statusReason" class="block text-sm font-medium text-gray-700">Reason for Cancellation *</label>
                            <textarea wire:model="statusReason" id="statusReason" rows="3" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                      placeholder="Please provide a reason for cancelling this encounter..."></textarea>
                            @error('statusReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showCancelModal', false)" 
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Cancel Encounter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
