<?php

use App\Models\Queue;
use App\Models\Encounter;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use function Livewire\Volt\{state, mount};

state([
    'queue' => null,
    'patient' => null,
    'encounter' => null,
    'doctor' => null,
    'followupData' => [
        'appointment_date' => '',
        'appointment_time' => '',
        'appointment_type' => 'follow_up',
        'notes' => '',
        'duration' => 30,
    ],
    'availableSlots' => [],
    'selectedDate' => '',
    'isScheduling' => false,
]);

mount(function (Queue $queue) {
    $this->queue = $queue;
    $this->patient = $queue->patient;
    $this->doctor = auth()->user()->doctor;
    
    // Get today's encounter
    $this->encounter = $this->patient->encounters()
        ->whereDate('encounter_date', now()->toDateString())
        ->where('doctor_id', $this->doctor->id)
        ->first();
    
    // Set default follow-up date (1 week from now)
    $this->followupData['appointment_date'] = now()->addWeek()->format('Y-m-d');
    $this->selectedDate = $this->followupData['appointment_date'];
    
    // Load available slots for the selected date
    $this->loadAvailableSlots();
});

$loadAvailableSlots = function () {
    if (!$this->selectedDate) {
        $this->availableSlots = [];
        return;
    }
    
    $date = Carbon::parse($this->selectedDate);
    $doctorId = $this->doctor->id;
    
    // Get existing appointments for the selected date
    $existingAppointments = Appointment::where('doctor_id', $doctorId)
        ->whereDate('appointment_date', $date)
        ->pluck('appointment_time')
        ->map(fn($time) => Carbon::parse($time)->format('H:i'))
        ->toArray();
    
    // Generate available time slots (9 AM to 5 PM, 30-minute intervals)
    $slots = [];
    $startTime = $date->copy()->setTime(9, 0);
    $endTime = $date->copy()->setTime(17, 0);
    
    while ($startTime->lte($endTime)) {
        $timeString = $startTime->format('H:i');
        if (!in_array($timeString, $existingAppointments)) {
            $slots[] = [
                'time' => $timeString,
                'display' => $startTime->format('g:i A'),
                'available' => true,
            ];
        }
        $startTime->addMinutes(30);
    }
    
    $this->availableSlots = $slots;
};

$selectDate = function ($date) {
    $this->selectedDate = $date;
    $this->followupData['appointment_date'] = $date;
    $this->loadAvailableSlots();
};

$selectTime = function ($time) {
    $this->followupData['appointment_time'] = $time;
};

$scheduleFollowup = function () {
    $this->validate([
        'followupData.appointment_date' => 'required|date|after:today',
        'followupData.appointment_time' => 'required',
        'followupData.notes' => 'nullable|string|max:500',
    ]);
    
    $this->isScheduling = true;
    
    try {
        // Create appointment
        $appointment = Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => $this->followupData['appointment_date'],
            'appointment_time' => $this->followupData['appointment_time'],
            'appointment_type' => $this->followupData['appointment_type'],
            'duration' => $this->followupData['duration'],
            'notes' => $this->followupData['notes'],
            'status' => 'scheduled',
        ]);
        
        // Update encounter with follow-up date
        if ($this->encounter) {
            $this->encounter->update([
                'follow_up_date' => $this->followupData['appointment_date'],
            ]);
        }
        
        // Complete the current queue
        $this->queue->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        session()->flash('success', 'Follow-up appointment scheduled successfully for ' . 
            Carbon::parse($this->followupData['appointment_date'])->format('M d, Y') . 
            ' at ' . Carbon::parse($this->followupData['appointment_time'])->format('g:i A'));
        
        // Dispatch event to go back to queue
        $this->dispatch('queue-completed');
        
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to schedule follow-up appointment: ' . $e->getMessage());
    } finally {
        $this->isScheduling = false;
    }
};

$skipFollowup = function () {
    // Complete the current queue without scheduling follow-up
    $this->queue->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    
    session()->flash('success', 'Consultation completed without follow-up appointment.');
    $this->dispatch('queue-completed');
};

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Follow-up Scheduling</h2>
                <p class="text-gray-600">Schedule a follow-up appointment for {{ $patient->full_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="skipFollowup" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Skip Follow-up
                </button>
                <button wire:click="scheduleFollowup" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="scheduleFollowup">
                        Schedule Appointment
                    </span>
                    <span wire:loading wire:target="scheduleFollowup">
                        Scheduling...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    @svg('heroicon-o-check-circle', 'w-5 h-5 text-green-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    @svg('heroicon-o-exclamation-triangle', 'w-5 h-5 text-red-400')
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Scheduling Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Appointment Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Appointment Type</label>
                        <select wire:model="followupData.appointment_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="follow_up">Follow-up Visit</option>
                            <option value="consultation">Consultation</option>
                            <option value="checkup">Check-up</option>
                            <option value="review">Review</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                        <select wire:model="followupData.duration" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea wire:model="followupData.notes" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="4"
                                  placeholder="Add any notes for the follow-up appointment..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Date Selection -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Date</h3>
                <div class="grid grid-cols-7 gap-2">
                    @for($i = 1; $i <= 14; $i++)
                        @php
                            $date = now()->addDays($i);
                            $dateString = $date->format('Y-m-d');
                            $isSelected = $selectedDate === $dateString;
                            $isWeekend = $date->isWeekend();
                        @endphp
                        <button wire:click="selectDate('{{ $dateString }}')" 
                                @class([
                                    'p-3 text-center rounded-lg border transition-colors',
                                    'bg-blue-100 border-blue-300 text-blue-800' => $isSelected,
                                    'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100' => !$isSelected && !$isWeekend,
                                    'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed' => $isWeekend,
                                ])
                                @if($isWeekend) disabled @endif>
                            <div class="text-sm font-medium">{{ $date->format('D') }}</div>
                            <div class="text-lg font-bold">{{ $date->format('j') }}</div>
                            <div class="text-xs">{{ $date->format('M') }}</div>
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Time Selection -->
            @if($selectedDate)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Available Times for {{ Carbon::parse($selectedDate)->format('l, M j, Y') }}
                    </h3>
                    <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2">
                        @forelse($availableSlots as $slot)
                            <button wire:click="selectTime('{{ $slot['time'] }}')" 
                                    @class([
                                        'p-3 text-center rounded-lg border transition-colors',
                                        'bg-blue-100 border-blue-300 text-blue-800' => $followupData['appointment_time'] === $slot['time'],
                                        'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100' => $followupData['appointment_time'] !== $slot['time'],
                                    ])>
                                {{ $slot['display'] }}
                            </button>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                <p>No available slots for this date.</p>
                                <p class="text-sm">Please select a different date.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Patient Info & Summary -->
        <div class="space-y-6">
            <!-- Patient Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Summary</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $patient->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Age</p>
                        <p class="font-medium">{{ $patient->date_of_birth ? $patient->date_of_birth->age . ' years' : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">{{ $patient->phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Doctor</p>
                        <p class="font-medium">Dr. {{ $doctor->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Appointment Summary -->
            @if($followupData['appointment_date'] && $followupData['appointment_time'])
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Appointment Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">Date:</span>
                            <span class="font-medium text-blue-900">{{ Carbon::parse($followupData['appointment_date'])->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Time:</span>
                            <span class="font-medium text-blue-900">{{ Carbon::parse($followupData['appointment_time'])->format('g:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Type:</span>
                            <span class="font-medium text-blue-900">{{ str($followupData['appointment_type'])->replace('_', ' ')->title() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">Duration:</span>
                            <span class="font-medium text-blue-900">{{ $followupData['duration'] }} minutes</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button wire:click="scheduleFollowup" 
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        <span wire:loading.remove wire:target="scheduleFollowup">
                            Schedule Appointment
                        </span>
                        <span wire:loading wire:target="scheduleFollowup">
                            Scheduling...
                        </span>
                    </button>
                    <button wire:click="skipFollowup" 
                            class="w-full px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Skip Follow-up
                    </button>
                    <button wire:click="$dispatch('back-to-queue')" 
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Queue
                    </button>
                </div>
            </div>

            <!-- Scheduling Guidelines -->
            <div class="bg-green-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-4">Scheduling Guidelines</h3>
                <ul class="text-sm text-green-800 space-y-2">
                    <li>• Follow-up appointments are typically scheduled 1-2 weeks after initial consultation</li>
                    <li>• Consider patient's condition and treatment plan when scheduling</li>
                    <li>• Patients will receive automatic notifications about their appointment</li>
                    <li>• Ensure adequate time is allocated based on the type of follow-up needed</li>
                </ul>
            </div>
        </div>
    </div>
</div>
