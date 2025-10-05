<?php

use App\Models\Appointment;
use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use function Livewire\Volt\{state, layout, mount};

state([
    'appointment' => null,
]);

layout('layouts.app');

mount(function ($appointment) {
    $this->appointment = $appointment;
});

$statusColors = fn() => [
    'scheduled' => 'bg-yellow-100 text-yellow-800',
    'confirmed' => 'bg-green-100 text-green-800',
    'in_progress' => 'bg-blue-100 text-blue-800',
    'completed' => 'bg-gray-100 text-gray-800',
    'cancelled' => 'bg-red-100 text-red-800',
    'no_show' => 'bg-orange-100 text-orange-800',
];

$typeColors = fn() => [
    'consultation' => 'bg-blue-100 text-blue-800',
    'follow_up' => 'bg-green-100 text-green-800',
    'emergency' => 'bg-red-100 text-red-800',
    'routine_checkup' => 'bg-purple-100 text-purple-800',
];

$edit = function () {
    $this->redirectRoute('appointments.edit', ['appointment' => $this->appointment->id]);
};

$back = function () {
    $this->redirectRoute('appointments.index');
};

$confirm = function () {
    $this->appointment->update(['status' => 'confirmed']);
    session()->flash('success', 'Appointment confirmed successfully.');
    $this->redirectRoute('appointments.show', ['appointment' => $this->appointment->id]);
};

$cancel = function () {
    $this->appointment->update(['status' => 'cancelled']);
    session()->flash('success', 'Appointment cancelled successfully.');
    $this->redirectRoute('appointments.show', ['appointment' => $this->appointment->id]);
};

$complete = function () {
    $this->appointment->update(['status' => 'completed']);
    session()->flash('success', 'Appointment completed successfully.');
    $this->redirectRoute('appointments.show', ['appointment' => $this->appointment->id]);
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
                <h1 class="text-2xl font-bold text-gray-900">Appointment Details</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time->format('h:i A') }}
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            <button wire:click="edit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Edit Appointment
            </button>
        </div>
    </div>

    <!-- Status Actions -->
    @if($appointment->status === 'scheduled')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-yellow-800 font-medium">This appointment is scheduled and awaiting confirmation.</span>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="confirm" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        Confirm
                    </button>
                    <button wire:click="cancel" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @elseif($appointment->status === 'confirmed')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 font-medium">This appointment is confirmed and ready.</span>
                </div>
                <div class="flex space-x-2">
                    <button wire:click="complete" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                        Mark Complete
                    </button>
                    <button wire:click="cancel" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @elseif($appointment->status === 'in_progress')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-blue-800 font-medium">This appointment is currently in progress.</span>
                </div>
                <button wire:click="complete" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                    Mark Complete
                </button>
            </div>
        </div>
    @endif

    <!-- Appointment Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Patient Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $appointment->patient->phone_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $appointment->patient->date_of_birth ? $appointment->patient->date_of_birth->format('M d, Y') : 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Gender</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($appointment->patient->gender ?? 'Not provided') }}</p>
                    </div>
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Appointment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Date</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $appointment->appointment_date->format('l, F d, Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Time</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $appointment->appointment_time->format('h:i A') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Duration</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $appointment->duration }} minutes</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Type</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->typeColors()[$appointment->type] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucwords(str_replace('_', ' ', $appointment->type)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($appointment->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $appointment->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->statusColors()[$appointment->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucwords(str_replace('_', ' ', $appointment->status)) }}
                    </span>
                </div>
            </div>

            <!-- Doctor Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Doctor</h3>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-lg font-medium text-gray-700">
                                {{ substr($appointment->doctor->first_name, 0, 1) }}{{ substr($appointment->doctor->last_name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $appointment->doctor->specialization ?? 'General Practice' }}</p>
                    </div>
                </div>
            </div>

            <!-- Clinic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Clinic</h3>
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-900">{{ $appointment->clinic->name }}</p>
                    @if($appointment->clinic->address)
                        <p class="text-sm text-gray-500">{{ $appointment->clinic->address }}</p>
                    @endif
                    @if($appointment->clinic->phone)
                        <p class="text-sm text-gray-500">{{ $appointment->clinic->phone }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <button wire:click="edit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                        Edit Appointment
                    </button>
                    @if($appointment->status === 'scheduled')
                        <button wire:click="confirm" class="w-full text-left px-3 py-2 text-sm text-green-700 hover:bg-green-50 rounded">
                            Confirm Appointment
                        </button>
                    @endif
                    @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                        <button wire:click="cancel" class="w-full text-left px-3 py-2 text-sm text-red-700 hover:bg-red-50 rounded">
                            Cancel Appointment
                        </button>
                    @endif
                    @if(in_array($appointment->status, ['confirmed', 'in_progress']))
                        <button wire:click="complete" class="w-full text-left px-3 py-2 text-sm text-blue-700 hover:bg-blue-50 rounded">
                            Mark Complete
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
