<?php

use App\Models\Queue;
use App\Models\ClinicDoctor;
use App\Events\QueueUpdated;
use function Livewire\Volt\{state, mount, on};

state([
    'todayQueue' => null,
    'stats' => [],
    'selectedClinic' => null,
    'clinics' => null,
]);

mount(function () {
    $doctor = auth()->user()->doctor;
    
    if (!$doctor) {
        $this->todayQueue = collect();
        $this->stats = [
            'total' => 0,
            'waiting' => 0,
            'in_progress' => 0,
            'completed' => 0,
        ];
        return;
    }

    // Get doctor's clinics
    $this->clinics = ClinicDoctor::with('clinic')
        ->where('doctor_id', $doctor->id)
        ->get();
    
    $this->selectedClinic = $this->clinics->first()?->clinic_id;
    
    $this->loadQueueData();
});

$loadQueueData = function () {
    $doctor = auth()->user()->doctor;
    if (!$doctor || !$this->selectedClinic) {
        $this->todayQueue = collect();
        $this->stats = [
            'total' => 0,
            'waiting' => 0,
            'in_progress' => 0,
            'completed' => 0,
        ];
        return;
    }

    $this->todayQueue = Queue::with(['patient', 'clinic'])
        ->whereDate('created_at', now()->toDateString())
        ->where('clinic_id', $this->selectedClinic)
        ->orderByRaw("CASE priority 
            WHEN 'emergency' THEN 1 
            WHEN 'urgent' THEN 2 
            WHEN 'high' THEN 3 
            WHEN 'normal' THEN 4 
            WHEN 'low' THEN 5 
            ELSE 6 END")
        ->orderBy('created_at', 'asc')
        ->get();

    $this->stats = [
        'total' => $this->todayQueue ? $this->todayQueue->count() : 0,
        'waiting' => $this->todayQueue ? $this->todayQueue->where('status', 'waiting')->count() : 0,
        'in_progress' => $this->todayQueue ? $this->todayQueue->where('status', 'in_progress')->count() : 0,
        'completed' => $this->todayQueue ? $this->todayQueue->where('status', 'completed')->count() : 0,
    ];
};

$selectQueue = function ($queueId) {
    $queue = Queue::find($queueId);
    
    if (!$queue) {
        return;
    }

    // Update queue status to in_progress
    $queue->update([
        'status' => 'in_progress',
        'called_at' => now(),
    ]);

    // Broadcast queue update
    broadcast(new QueueUpdated("Queue {$queue->queue_number} is now in progress!", 'in_progress'))->toOthers();

    // Dispatch event to parent component
    $this->dispatch('selected-queue', $queue->id);
};

$updateClinic = function ($clinicId) {
    $this->selectedClinic = $clinicId;
    $this->loadQueueData();
};

on([
    'echo:queues,QueueUpdated' => $loadQueueData,
]);

?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Queue Monitoring</h2>
                <p class="text-gray-600">Monitor and manage patient consultations for today</p>
            </div>
            <div class="flex items-center space-x-4">
                @if($clinics && $clinics->count() > 1)
                    <select wire:model.live="selectedClinic" wire:change="updateClinic($event.target.value)" 
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($clinics as $clinicDoctor)
                            <option value="{{ $clinicDoctor->clinic_id }}">
                                {{ $clinicDoctor->clinic->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <button wire:click="loadQueueData" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        @svg('heroicon-o-users', 'w-6 h-6 text-blue-600')
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600">Total Patients</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        @svg('heroicon-o-clock', 'w-6 h-6 text-yellow-600')
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600">Waiting</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $stats['waiting'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        @svg('heroicon-o-play', 'w-6 h-6 text-blue-600')
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600">In Progress</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        @svg('heroicon-o-check-circle', 'w-6 h-6 text-green-600')
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600">Completed</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Queue List -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Today's Patient Queue</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($todayQueue ?? [] as $queue)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Queue Number -->
                            <div class="flex-shrink-0">
                                <div @class([
                                    'w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg',
                                    'bg-red-500' => $queue->priority === 'emergency',
                                    'bg-orange-500' => $queue->priority === 'urgent',
                                    'bg-yellow-500' => $queue->priority === 'high',
                                    'bg-blue-500' => $queue->priority === 'normal',
                                    'bg-gray-500' => $queue->priority === 'low',
                                ])>
                                    {{ $loop->iteration }}
                                </div>
                            </div>
                            
                            <!-- Patient Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $queue->patient->full_name }}
                                    </h4>
                                    @if($queue->priority !== 'normal')
                                        <span @class([
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            'bg-red-100 text-red-800' => $queue->priority === 'emergency',
                                            'bg-orange-100 text-orange-800' => $queue->priority === 'urgent',
                                            'bg-yellow-100 text-yellow-800' => $queue->priority === 'high',
                                            'bg-gray-100 text-gray-800' => $queue->priority === 'low',
                                        ])>
                                            {{ str($queue->priority)->title() }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        @svg('heroicon-o-phone', 'w-4 h-4 mr-1')
                                        {{ $queue->patient->phone }}
                                    </span>
                                    <span class="flex items-center">
                                        @svg('heroicon-o-clock', 'w-4 h-4 mr-1')
                                        {{ $queue->created_at->format('h:ia') }}
                                    </span>
                                    @if($queue->patient->date_of_birth)
                                        <span class="flex items-center">
                                            @svg('heroicon-o-cake', 'w-4 h-4 mr-1')
                                            {{ $queue->patient->date_of_birth->age }} years
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status and Actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Status Badge -->
                            <span @class([
                                'px-3 py-1 text-sm font-medium rounded-full',
                                'bg-yellow-100 text-yellow-800' => $queue->status === 'waiting',
                                'bg-blue-100 text-blue-800' => $queue->status === 'in_progress',
                                'bg-green-100 text-green-800' => $queue->status === 'completed',
                                'bg-gray-100 text-gray-800' => !in_array($queue->status, ['waiting', 'in_progress', 'completed']),
                            ])>
                                {{ str($queue->status)->replace('_', ' ')->title() }}
                            </span>
                            
                            <!-- Action Button -->
                            @if($queue->status === 'waiting')
                                <button wire:click="selectQueue({{ $queue->id }})" 
                                        wire:loading.attr="disabled"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="selectQueue({{ $queue->id }})">
                                        Start Consultation
                                    </span>
                                    <span wire:loading wire:target="selectQueue({{ $queue->id }})">
                                        Starting...
                                    </span>
                                </button>
                            @elseif($queue->status === 'in_progress')
                                <button wire:click="selectQueue({{ $queue->id }})" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Continue Consultation
                                </button>
                            @else
                                <span class="text-sm text-gray-500">Completed</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        @svg('heroicon-o-queue-list', 'w-8 h-8 text-gray-400')
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No patients in queue</h3>
                    <p class="text-gray-500">No patients are currently queued for consultation today.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
