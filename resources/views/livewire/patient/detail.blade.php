<?php

use Faker\Generator as Faker;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;
use function Livewire\Volt\{layout, state, form, mount, title};

state(['patientId'])->url();

state([
    'patient' => fn() => Patient::find($this->patientId),
]);

// Add new state for active tab
state(['activeTab' => 'overview']); // Default tab

// Add methods to handle tab switching
$setActiveTab = function ($tab) {
    $this->activeTab = $tab;
};

layout('layouts.app');

$goback = function () {
    $this->redirect('/patients', navigate: true);
};

?>

<section class="min-h-screen bg-gray-50">
    <!-- Header Banner with Patient Info -->
    <div class="bg-white border-b">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between py-6">
                <div class="flex items-center space-x-5">
                    <button 
                        wire:click="goback"
                        class="p-2 text-gray-600 transition-colors rounded-full hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-5">
                        <!-- Patient Avatar -->
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-semibold text-blue-600">
                                        {{ substr($patient->full_name, 0, 1) }}
                                    </span>
                                </div>
                                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-400"></span>
                            </div>
                        </div>
                        <!-- Patient Info -->
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ $patient->full_name }}
                            </h2>
                            <div class="flex items-center space-x-4 mt-1">
                                <p class="text-sm text-gray-500">ID: #{{ $patient->id }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Active Patient
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 mt-4 lg:mt-0">
                    <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit Profile
                    </button>
                    <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Encounter
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="border-t">
                <nav class="-mb-px flex space-x-8">
                    <button 
                        wire:click="setActiveTab('overview')"
                        class="{{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Overview
                    </button>
                    <button 
                        wire:click="setActiveTab('encounters')"
                        class="{{ $activeTab === 'encounters' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Encounters
                    </button>
                    <button 
                        wire:click="setActiveTab('records')"
                        class="{{ $activeTab === 'records' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Medical Records
                    </button>
                    <button 
                        wire:click="setActiveTab('prescriptions')"
                        class="{{ $activeTab === 'prescriptions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Prescriptions
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <!-- Overview Tab Content -->
            @if ($activeTab === 'overview')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Personal Information</h3>
                            <livewire:patient.profile :patient="$patient" />
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Contact Details</h3>
                            <livewire:patient.address :patient="$patient" />
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-9 space-y-6">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-gray-900">Recent Activity</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Last Updated Today
                                </span>
                            </div>
                            <livewire:patient.encounter.index :patient="$patient" limit="5" />
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Medical History</h3>
                            <livewire:patient.record.index :patient="$patient" />
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Medical Records Tab Content -->
            @if ($activeTab === 'records')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column - Medical History -->
                <div class="lg:col-span-8">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Medical History</h3>
                                <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Record
                                </button>
                            </div>
                            <livewire:patient.record.index :patient="$patient" />
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary & Stats -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Record Summary</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Records</span>
                                    <span class="font-medium">24</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Last Updated</span>
                                    <span class="font-medium">Today</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Record Types</span>
                                    <span class="font-medium">8</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload Documents
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                    </svg>
                                    Generate Report
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Archive Records
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Encounters Tab Content -->
            @if ($activeTab === 'encounters')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column - Encounters List -->
                <div class="lg:col-span-8">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Patient Encounters</h3>
                                <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Encounter
                                </button>
                            </div>

                            <!-- Encounter Filters -->
                            <div class="mb-6 flex items-center space-x-4">
                                <div class="relative">
                                    <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>All Types</option>
                                        <option>Follow-up</option>
                                        <option>Initial Visit</option>
                                        <option>Emergency</option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>Last 30 Days</option>
                                        <option>Last 3 Months</option>
                                        <option>Last 6 Months</option>
                                        <option>Last Year</option>
                                    </select>
                                </div>
                            </div>

                            <livewire:patient.encounter.index :patient="$patient" />
                        </div>
                    </div>
                </div>

                <!-- Right Column - Stats & Quick Info -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Encounter Statistics -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Encounter Summary</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Encounters</span>
                                    <span class="font-medium">32</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Last Visit</span>
                                    <span class="font-medium">2 days ago</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Average Duration</span>
                                    <span class="font-medium">45 mins</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Appointment -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Next Appointment</h3>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-blue-800">Follow-up Visit</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Scheduled
                                    </span>
                                </div>
                                <div class="text-sm text-blue-900">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        March 15, 2024
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        10:30 AM
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Schedule Appointment
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Generate Report
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    Set Reminder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Prescriptions Tab Content -->
            @if ($activeTab === 'prescriptions')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column - Prescriptions List -->
                <div class="lg:col-span-8">
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Prescriptions</h3>
                                <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Prescription
                                </button>
                            </div>

                            <!-- Prescription Filters -->
                            <div class="mb-6 flex flex-wrap gap-4">
                                <div class="relative">
                                    <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>All Status</option>
                                        <option>Active</option>
                                        <option>Completed</option>
                                        <option>Discontinued</option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option>Last 30 Days</option>
                                        <option>Last 3 Months</option>
                                        <option>Last 6 Months</option>
                                        <option>All Time</option>
                                    </select>
                                </div>
                                <div class="relative flex-grow">
                                    <input 
                                        type="text" 
                                        placeholder="Search medications..."
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    >
                                </div>
                            </div>

                            <!-- Prescriptions List -->
                            <div class="space-y-4">
                                <livewire:patient.prescription.index :patient="$patient" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Active Medications -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Active Medications</h3>
                            <div class="space-y-4">
                                <div class="p-4 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-yellow-800">Allergies & Warnings</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Important
                                        </span>
                                    </div>
                                    <p class="text-sm text-yellow-700">Patient has reported allergies to penicillin</p>
                                </div>
                                
                                <div class="border-t pt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Current Medications</span>
                                        <span class="font-medium">4 Active</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Pending Refills</span>
                                        <span class="font-medium">2</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Last Updated</span>
                                        <span class="font-medium">Today</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription History Summary -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Prescription History</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Prescriptions</span>
                                    <span class="font-medium">28</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Active Prescriptions</span>
                                    <span class="font-medium">4</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Discontinued</span>
                                    <span class="font-medium">6</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Print Prescription
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Request Refill
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Schedule Follow-up
                                </button>
                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View History Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
