<?php

use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Encounter;
use App\Enums\PrescriptionStatusEnum;
use function Livewire\Volt\{state, layout, mount, rules, computed};

state(['prescriptionId'])->url();

state([
    'prescription' => null,
    'patient_id' => '',
    'doctor_id' => '',
    'encounter_id' => '',
    'medication_name' => '',
    'dosage' => '',
    'frequency' => '',
    'quantity' => '',
    'refills' => 0,
    'instructions' => '',
    'status' => 'active',
    'start_date' => '',
    'end_date' => '',
    'isSubmitting' => false,
]);

layout('layouts.app');

rules([
    'patient_id' => 'required|exists:patients,id',
    'doctor_id' => 'required|exists:doctors,id',
    'medication_name' => 'required|string|max:255',
    'dosage' => 'required|string|max:100',
    'frequency' => 'required|string|max:100',
    'quantity' => 'nullable|integer|min:1',
    'refills' => 'nullable|integer|min:0',
    'instructions' => 'nullable|string',
    'status' => 'required|in:active,completed,cancelled',
    'start_date' => 'required|date',
    'end_date' => 'nullable|date|after:start_date',
]);

mount(function () {
    if ($this->prescriptionId) {
        // Handle both scalar ID and model instance from route model binding
        if (is_object($this->prescriptionId) && $this->prescriptionId instanceof Prescription) {
            $this->prescription = $this->prescriptionId;
        } else {
            $this->prescription = Prescription::find($this->prescriptionId);
        }
        
        if ($this->prescription) {
            $this->patient_id = $this->prescription->patient_id;
            $this->doctor_id = $this->prescription->doctor_id;
            $this->encounter_id = $this->prescription->encounter_id;
            $this->medication_name = $this->prescription->medication_name;
            $this->dosage = $this->prescription->dosage;
            $this->frequency = $this->prescription->frequency;
            $this->quantity = $this->prescription->quantity;
            $this->refills = $this->prescription->refills;
            $this->instructions = $this->prescription->instructions;
            $this->status = $this->prescription->status;
            $this->start_date = $this->prescription->start_date?->format('Y-m-d');
            $this->end_date = $this->prescription->end_date?->format('Y-m-d');
        }
    } else {
        // New prescription - set defaults
        $this->doctor_id = auth()->user()->doctor?->id;
        $this->start_date = now()->format('Y-m-d');
    }
});

$patients = computed(function () {
    return Patient::orderBy('first_name')->orderBy('last_name')->get();
});

$doctors = computed(function () {
    return Doctor::with('user')->get();
});

$encounters = computed(function () {
    if ($this->patient_id) {
        return Encounter::where('patient_id', $this->patient_id)
            ->orderBy('encounter_date', 'desc')
            ->get();
    }
    return collect();
});

$statuses = fn() => [
    'active' => 'Active',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
];

$save = function () {
    $this->validate();
    
    $this->isSubmitting = true;
    
    try {
        $data = [
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'encounter_id' => $this->encounter_id ?: null,
            'medication_name' => $this->medication_name,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'quantity' => $this->quantity ?: null,
            'refills' => $this->refills ?: 0,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
        ];
        
        if ($this->prescription) {
            $this->prescription->update($data);
            session()->flash('message', 'Prescription updated successfully.');
        } else {
            $this->prescription = Prescription::create($data);
            session()->flash('message', 'Prescription created successfully.');
        }
        
        $this->redirectRoute('prescriptions.show', ['prescription' => $this->prescription]);
        
    } catch (\Exception $e) {
        session()->flash('error', 'An error occurred while saving the prescription.');
    } finally {
        $this->isSubmitting = false;
    }
};

$cancel = function () {
    if ($this->prescription) {
        $this->redirectRoute('prescriptions.show', ['prescription' => $this->prescription]);
    } else {
        $this->redirectRoute('prescriptions.index');
    }
};

?>

<section class="min-h-screen bg-gray-50/30 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $prescription ? 'Edit Prescription' : 'New Prescription' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $prescription ? 'Update prescription details' : 'Create a new prescription for a patient' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="cancel"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form wire:submit="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Patient <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="patient_id" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Select a patient</option>
                                @foreach($this->patients as $patient)
                                    <option value="{{ $patient->id }}">
                                        {{ $patient->first_name }} {{ $patient->last_name }} (ID: {{ $patient->patient_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Doctor Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Doctor <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="doctor_id" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Select a doctor</option>
                                @foreach($this->doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
                                @endforeach
                            </select>
                            @error('doctor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Encounter Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Encounter (Optional)
                            </label>
                            <select wire:model="encounter_id" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Select an encounter</option>
                                @foreach($this->encounters as $encounter)
                                    <option value="{{ $encounter->id }}">
                                        {{ $encounter->encounter_date }} - {{ $encounter->chief_complaint }}
                                    </option>
                                @endforeach
                            </select>
                            @error('encounter_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Medication Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Medication Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="medication_name"
                                   placeholder="Enter medication name"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('medication_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dosage -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Dosage <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="dosage"
                                   placeholder="e.g., 500mg, 1 tablet"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('dosage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Frequency -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Frequency <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="frequency"
                                   placeholder="e.g., Twice daily, Every 8 hours"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('frequency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantity
                            </label>
                            <input type="number" 
                                   wire:model="quantity"
                                   placeholder="Number of units"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Refills -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Refills
                            </label>
                            <input type="number" 
                                   wire:model="refills"
                                   min="0"
                                   placeholder="Number of refills"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('refills') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="status" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @foreach($this->statuses() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model="start_date"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Date
                            </label>
                            <input type="date" 
                                   wire:model="end_date"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Instructions
                        </label>
                        <textarea wire:model="instructions"
                                  rows="4"
                                  placeholder="Additional instructions for the patient..."
                                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                        @error('instructions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="cancel"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">
                                {{ $prescription ? 'Update Prescription' : 'Create Prescription' }}
                            </span>
                            <span wire:loading wire:target="save">
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
