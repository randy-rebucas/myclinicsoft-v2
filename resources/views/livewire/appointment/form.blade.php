<?php

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\ClinicDoctor;
use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use function Livewire\Volt\{state, layout, mount, computed};

state([
    'appointment' => null,
    'isEditing' => false,
    // Form properties
    'patient_id' => '',
    'doctor_id' => '',
    'clinic_id' => '',
    'appointment_date' => '',
    'appointment_time' => '',
    'duration' => 30,
    'type' => 'consultation',
    'status' => 'scheduled',
    'notes' => '',
]);

layout('layouts.app');

mount(function ($appointment = null) {
    if ($appointment) {
        $this->appointment = $appointment;
        $this->isEditing = true;
        
        $this->patient_id = $appointment->patient_id;
        $this->doctor_id = $appointment->doctor_id;
        $this->clinic_id = $appointment->clinic_id;
        $this->appointment_date = $appointment->appointment_date->format('Y-m-d');
        $this->appointment_time = $appointment->appointment_time->format('H:i');
        $this->duration = $appointment->duration;
        $this->type = $appointment->type;
        $this->status = $appointment->status;
        $this->notes = $appointment->notes;
    } else {
        $this->appointment_date = now()->format('Y-m-d');
        $this->appointment_time = now()->addHour()->format('H:i');
    }
});

$patients = computed(function () {
    return Patient::orderBy('first_name')->orderBy('last_name')->get();
});

$doctors = computed(function () {
    return Doctor::with('user')->orderBy('first_name')->orderBy('last_name')->get();
});

$clinics = computed(function () {
    return ClinicDoctor::with('clinic')
        ->where('doctor_id', $this->doctor_id ?: auth()->user()->doctor->id)
        ->get();
});

$statuses = fn() => [
    'scheduled' => 'Scheduled',
    'confirmed' => 'Confirmed',
    'in_progress' => 'In Progress',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    'no_show' => 'No Show',
];

$types = fn() => [
    'consultation' => 'Consultation',
    'follow_up' => 'Follow Up',
    'emergency' => 'Emergency',
    'routine_checkup' => 'Routine Checkup',
];

$save = function () {
    $this->validate([
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:doctors,id',
        'clinic_id' => 'required|exists:clinics,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'duration' => 'required|integer|min:15|max:240',
        'type' => 'required|in:consultation,follow_up,emergency,routine_checkup',
        'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
        'notes' => 'nullable|string|max:1000',
    ]);

    $appointmentData = [
        'patient_id' => $this->patient_id,
        'doctor_id' => $this->doctor_id,
        'clinic_id' => $this->clinic_id,
        'appointment_date' => $this->appointment_date,
        'appointment_time' => $this->appointment_time,
        'duration' => $this->duration,
        'type' => $this->type,
        'status' => $this->status,
        'notes' => $this->notes,
    ];

    if ($this->isEditing) {
        $this->appointment->update($appointmentData);
        session()->flash('success', 'Appointment updated successfully.');
    } else {
        Appointment::create($appointmentData);
        session()->flash('success', 'Appointment created successfully.');
    }

    $this->redirectRoute('appointments.index');
};

$cancel = function () {
    $this->redirectRoute('appointments.index');
};

$back = function () {
    $this->redirectRoute('appointments.index');
};
?>


<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button wire:click="back" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $isEditing ? 'Edit Appointment' : 'Create New Appointment' }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isEditing ? 'Update appointment details' : 'Schedule a new patient appointment' }}
                </p>
            </div>
        </div>
        <button wire:click="cancel" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            Cancel
        </button>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Selection -->
                <div class="md:col-span-2">
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Patient <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="patient_id" 
                        id="patient_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Select a patient</option>
                        @foreach($this->patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->first_name }} {{ $patient->last_name }} 
                                ({{ $patient->phone_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Doctor Selection -->
                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Doctor <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="doctor_id" 
                        id="doctor_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Select a doctor</option>
                        @foreach($this->doctors as $doctor)
                            <option value="{{ $doctor->id }}">
                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Clinic Selection -->
                <div>
                    <label for="clinic_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Clinic <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="clinic_id" 
                        id="clinic_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="">Select a clinic</option>
                        @foreach($this->clinics as $clinicDoctor)
                            <option value="{{ $clinicDoctor->clinic->id }}">
                                {{ $clinicDoctor->clinic->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('clinic_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Appointment Date -->
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model="appointment_date" 
                        id="appointment_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    @error('appointment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Appointment Time -->
                <div>
                    <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="time" 
                        wire:model="appointment_time" 
                        id="appointment_time"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    @error('appointment_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                        Duration (minutes) <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="duration" 
                        id="duration"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="15">15 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                    @error('duration') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="type" 
                        id="type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        @foreach($this->types() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="status" 
                        id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        @foreach($this->statuses() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes
                </label>
                <textarea 
                    wire:model="notes" 
                    id="notes"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Additional notes about the appointment..."
                ></textarea>
                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    wire:click="cancel"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    {{ $isEditing ? 'Update Appointment' : 'Create Appointment' }}
                </button>
            </div>
        </form>
    </div>
</div>
