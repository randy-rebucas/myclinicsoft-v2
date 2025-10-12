<?php

use App\Models\Appointment;
use App\Models\ClinicDoctor;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use function Livewire\Volt\{state, layout, mount, computed, with, usesPagination};

state([
    'search' => '',
    'filter' => 'all',
    'clinic_id' => '',
    'date_from' => '',
    'date_to' => '',
    'selectedAppointments' => [],
    'showBulkActions' => false,
    'showCreateModal' => false,
    // Form properties
    'patient_id' => '',
    'doctor_id' => '',
    'appointment_date' => '',
    'appointment_time' => '',
    'duration' => 30,
    'type' => 'consultation',
    'status' => 'scheduled',
    'notes' => '',
]);

// Form properties are handled in state

layout('layouts.app');

usesPagination();

mount(function () {
    $this->date_from = now()->startOfMonth()->format('Y-m-d');
    $this->date_to = now()->endOfMonth()->format('Y-m-d');
    $this->appointment_date = now()->format('Y-m-d');
    $this->appointment_time = now()->addHour()->format('H:i');
});

$appointments = computed(function () {
    return Appointment::with(['patient', 'doctor', 'clinic'])
        ->when($this->filter !== 'all', function ($query) {
            $query->where('status', $this->filter);
        })
        ->when($this->clinic_id, function ($query) {
            $query->where('clinic_id', $this->clinic_id);
        })
        ->when($this->date_from, function ($query) {
            $query->where('appointment_date', '>=', $this->date_from);
        })
        ->when($this->date_to, function ($query) {
            $query->where('appointment_date', '<=', $this->date_to);
        })
        ->when($this->search, function ($query) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            })->orWhereHas('doctor', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy('appointment_date', 'desc')
        ->orderBy('appointment_time', 'asc')
        ->paginate(15);
});

$clinics = computed(function () {
    return ClinicDoctor::with('clinic')
        ->where('doctor_id', auth()->user()->doctor->id)
        ->get();
});

$patients = computed(function () {
    return Patient::orderBy('first_name')->orderBy('last_name')->get();
});

$doctors = computed(function () {
    return Doctor::with('user')->orderBy('first_name')->orderBy('last_name')->get();
});

$modalClinics = computed(function () {
    return ClinicDoctor::with('clinic')
        ->where('doctor_id', $this->doctor_id ?: auth()->user()->doctor->id)
        ->get();
});

$statuses = fn() => [
    'all' => 'All Statuses',
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

$clearFilters = function () {
    $this->search = '';
    $this->filter = 'all';
    $this->clinic_id = '';
    $this->date_from = now()->startOfMonth()->format('Y-m-d');
    $this->date_to = now()->endOfMonth()->format('Y-m-d');
    $this->selectedAppointments = [];
    $this->showBulkActions = false;
};

$toggleSelection = function ($appointmentId) {
    if (in_array($appointmentId, $this->selectedAppointments)) {
        $this->selectedAppointments = array_filter($this->selectedAppointments, fn($id) => $id !== $appointmentId);
    } else {
        $this->selectedAppointments[] = $appointmentId;
    }
    
    $this->showBulkActions = count($this->selectedAppointments) > 0;
};

$selectAll = function () {
    $this->selectedAppointments = $this->appointments->pluck('id')->toArray();
    $this->showBulkActions = true;
};

$deselectAll = function () {
    $this->selectedAppointments = [];
    $this->showBulkActions = false;
};

$view = function ($appointmentId) {
    $this->redirectRoute('appointments.show', ['appointment' => $appointmentId]);
};

$edit = function ($appointmentId) {
    $this->redirectRoute('appointments.edit', ['appointment' => $appointmentId]);
};

$openCreateModal = function () {
    $this->showCreateModal = true;
    $this->resetForm();
};

$closeCreateModal = function () {
    $this->showCreateModal = false;
    $this->resetForm();
};

$resetForm = function () {
    $this->patient_id = '';
    $this->doctor_id = '';
    $this->clinic_id = '';
    $this->appointment_date = now()->format('Y-m-d');
    $this->appointment_time = now()->addHour()->format('H:i');
    $this->duration = 30;
    $this->type = 'consultation';
    $this->status = 'scheduled';
    $this->notes = '';
};

$createAppointment = function () {
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

    Appointment::create([
        'patient_id' => $this->patient_id,
        'doctor_id' => $this->doctor_id,
        'clinic_id' => $this->clinic_id,
        'appointment_date' => $this->appointment_date,
        'appointment_time' => $this->appointment_time,
        'duration' => $this->duration,
        'type' => $this->type,
        'status' => $this->status,
        'notes' => $this->notes,
    ]);

    session()->flash('success', 'Appointment created successfully.');
    $this->closeCreateModal();
};

?>

<section class="min-h-screen bg-gray-50/30 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Management</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage patient appointments and schedules</p>
                </div>
                <div class="flex space-x-3">
                     <button wire:click="openCreateModal"
                         class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                         </svg>
                         New Appointment
                     </button>
                </div>
            </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by patient or doctor name..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($this->statuses() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clinic Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clinic</label>
                <select wire:model.live="clinic_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Clinics</option>
                    @foreach($this->clinics as $clinicDoctor)
                        <option value="{{ $clinicDoctor->clinic->id }}">{{ $clinicDoctor->clinic->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="clearFilters" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input 
                    type="date" 
                    wire:model.live="date_from"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input 
                    type="date" 
                    wire:model.live="date_to"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($showBulkActions)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-blue-700">
                    {{ count($selectedAppointments) }} appointment(s) selected
                </span>
                <div class="flex gap-2">
                    <button wire:click="deselectAll" class="text-sm text-blue-600 hover:text-blue-800">
                        Deselect All
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Appointments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input 
                                type="checkbox" 
                                wire:click="selectAll"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Doctor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Clinic
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->appointments as $appointment)
                        <tr class="group hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input 
                                    type="checkbox" 
                                    wire:click="toggleSelection({{ $appointment->id }})"
                                    @checked(in_array($appointment->id, $selectedAppointments))
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($appointment->patient->first_name, 0, 1) }}{{ substr($appointment->patient->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $appointment->patient->phone_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->clinic->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $appointment->appointment_time->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $appointment->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'scheduled' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'no_show' => 'bg-orange-100 text-orange-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <!-- View Appointment Icon -->
                                    <button wire:click="view({{ $appointment->id }})" 
                                            class="p-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-200"
                                            title="View Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>

                                    <!-- Edit Appointment Icon -->
                                    <button wire:click="edit({{ $appointment->id }})" 
                                            class="p-1.5 rounded-full hover:bg-indigo-100 dark:hover:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors duration-200"
                                            title="Edit Appointment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No appointments found</p>
                                    <p class="text-sm">Try adjusting your search criteria or create a new appointment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($this->appointments->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $this->appointments->links() }}
            </div>
        @endif
    </div>
    </div>

    <!-- Create Appointment Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeCreateModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Create New Appointment</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="mt-6">
                        <form wire:submit="createAppointment" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                        @foreach($this->modalClinics as $clinicDoctor)
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
                                            @if($value !== 'all')
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endif
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
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Additional notes about the appointment..."
                                ></textarea>
                                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end space-x-3 pt-4 border-t">
                                <button 
                                    type="button" 
                                    wire:click="closeCreateModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Create Appointment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
                                </section>
