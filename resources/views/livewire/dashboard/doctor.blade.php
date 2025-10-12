<?php

use App\Models\Queue;
use function Livewire\Volt\{state, on};
state([
    'selectedQueue' => null,
    'activeView' => 'queue', // queue, consultation, diagnosis, prescription, followup
]);

on([
    'selected-queue' => function ($queueId) {
        $this->selectedQueue = Queue::findOrFail($queueId);
        $this->activeView = 'consultation';
    },
    'queue-completed' => function () {
        $this->selectedQueue = null;
        $this->activeView = 'queue';
    },
    'start-diagnosis' => function () {
        $this->activeView = 'diagnosis';
    },
    'start-prescription' => function () {
        $this->activeView = 'prescription';
    },
    'start-followup' => function () {
        $this->activeView = 'followup';
    },
    'back-to-queue' => function () {
        $this->activeView = 'queue';
        $this->selectedQueue = null;
    },
]);

?>

<div class="bg-gray-50 min-h-screen">
    <!-- Navigation Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-semibold text-gray-900">Doctor Dashboard</h1>
                    <nav class="flex space-x-4">
                        <button wire:click="$set('activeView', 'queue')" 
                                @class([
                                    'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                    'bg-blue-100 text-blue-700' => $activeView === 'queue',
                                    'text-gray-500 hover:text-gray-700 hover:bg-gray-100' => $activeView !== 'queue'
                                ])>
                            Queue Monitoring
                        </button>
                        @if($selectedQueue)
                            <button wire:click="$set('activeView', 'consultation')" 
                                    @class([
                                        'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                        'bg-blue-100 text-blue-700' => $activeView === 'consultation',
                                        'text-gray-500 hover:text-gray-700 hover:bg-gray-100' => $activeView !== 'consultation'
                                    ])>
                                Patient Processing
                            </button>
                            <button wire:click="$set('activeView', 'diagnosis')" 
                                    @class([
                                        'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                        'bg-blue-100 text-blue-700' => $activeView === 'diagnosis',
                                        'text-gray-500 hover:text-gray-700 hover:bg-gray-100' => $activeView !== 'diagnosis'
                                    ])>
                                Diagnosis Entry
                            </button>
                            <button wire:click="$set('activeView', 'prescription')" 
                                    @class([
                                        'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                        'bg-blue-100 text-blue-700' => $activeView === 'prescription',
                                        'text-gray-500 hover:text-gray-700 hover:bg-gray-100' => $activeView !== 'prescription'
                                    ])>
                                Prescription
                            </button>
                            <button wire:click="$set('activeView', 'followup')" 
                                    @class([
                                        'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                                        'bg-blue-100 text-blue-700' => $activeView === 'followup',
                                        'text-gray-500 hover:text-gray-700 hover:bg-gray-100' => $activeView !== 'followup'
                                    ])>
                                Follow-up
                            </button>
                        @endif
                    </nav>
                </div>
                @if($selectedQueue)
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            Patient: <span class="font-medium">{{ $selectedQueue->patient->full_name }}</span>
                        </span>
                        <button wire:click="$dispatch('back-to-queue')" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to Queue
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @switch($activeView)
            @case('queue')
                <livewire:doctor.queue-monitor />
            @break
            
            @case('consultation')
                @if($selectedQueue)
                    <livewire:doctor.patient-consultation :queue="$selectedQueue" />
                @endif
            @break
            
            @case('diagnosis')
                @if($selectedQueue)
                    <livewire:doctor.diagnosis-entry :queue="$selectedQueue" />
                @endif
            @break
            
            @case('prescription')
                @if($selectedQueue)
                    <livewire:doctor.prescription-generator :queue="$selectedQueue" />
                @endif
            @break
            
            @case('followup')
                @if($selectedQueue)
                    <livewire:doctor.followup-scheduler :queue="$selectedQueue" />
                @endif
            @break
            
            @default
                <livewire:doctor.queue-monitor />
        @endswitch
    </div>
</div>
