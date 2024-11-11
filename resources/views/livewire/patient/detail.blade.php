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

layout('layouts.app');

$goback = function () {
    $this->redirect('/patients', navigate: true);
};

?>

<section class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <x-slot name="header">
        <div class="flex items-center justify-between py-3">
            <div class="flex items-center space-x-4">
                <button 
                    wire:click="goback"
                    class="p-2 text-gray-600 transition-colors rounded-full hover:bg-gray-100"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $patient->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500">Patient ID: #{{ $patient->id }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Edit
                </button>
                <button class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Encounter
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Profile</h3>
                            <livewire:patient.profile :patient="$patient" />
                        </div>
                    </div>
                    
                    <div class="overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                            <livewire:patient.address :patient="$patient" />
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-9 space-y-6">
                    <div class="overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Encounters</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Latest Activity
                                </span>
                            </div>
                            <livewire:patient.encounter.index :patient="$patient" />
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Records</h3>
                            <livewire:patient.record.index :patient="$patient" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
