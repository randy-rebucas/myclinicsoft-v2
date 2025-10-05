<?php

use App\Models\Queue;
use App\Events\QueueUpdated;
use App\Models\ClinicDoctor;
use App\Enums\QueueStatusEnum;
use App\Enums\QueuePriorityEnum;
use Illuminate\Support\Str;
use function Livewire\Volt\{state, layout, form, mount, computed, with, usesPagination};

state([
    'clinics' => [],
    'filter' => 'waiting',
    'clinic_id' => '',
    'search' => '',
    'selectedQueues' => [],
    'showBulkActions' => false,
]);

layout('layouts.app');

usesPagination();

mount(function () {
    $this->clinics = ClinicDoctor::with('clinic')
        ->where('doctor_id', auth()->user()->doctor->id)
        ->get();
});

$queues = computed(function () {
    return Queue::with(['patient', 'clinic', 'doctor'])
        ->when($this->filter !== 'all', function ($query) {
            $query->where('status', $this->filter);
        })
        ->when($this->clinic_id, function ($query) {
            $query->where('clinic_id', $this->clinic_id);
        })
        ->when($this->search, function ($query) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->orWhere('queue_number', 'like', '%' . $this->search . '%');
        })
        ->orderByRaw("CASE priority 
            WHEN 'emergency' THEN 1 
            WHEN 'urgent' THEN 2 
            WHEN 'high' THEN 3 
            WHEN 'normal' THEN 4 
            WHEN 'low' THEN 5 
            ELSE 6 END")
        ->orderBy('created_at', 'asc')
        ->paginate(15);
});

// Helper function to update queue status
$updateQueueStatus = function ($queueId, $status, $message) {
    $queue = Queue::findOrFail($queueId);
    
    $updateData = ['status' => $status];
    
    if ($status === 'in_progress') {
        $updateData['called_at'] = now();
    } elseif ($status === 'completed') {
        $updateData['completed_at'] = now();
    }
    
    $queue->update($updateData);
    
    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated($message, $status))->toOthers();
    
    session()->flash('success', $message);
};

$callNext = function ($queueId) {
    $queue = Queue::findOrFail($queueId);
    $this->updateQueueStatus($queueId, 'in_progress', "Queue {$queue->queue_number} is now in progress!");
};

$complete = function ($queueId) {
    $queue = Queue::findOrFail($queueId);
    $this->updateQueueStatus($queueId, 'completed', "Queue {$queue->queue_number} is now completed!");
};

$cancel = function ($queueId) {
    $queue = Queue::findOrFail($queueId);
    $this->updateQueueStatus($queueId, 'cancelled', "Queue {$queue->queue_number} has been cancelled!");
};

$noShow = function ($queueId) {
    $queue = Queue::findOrFail($queueId);
    $this->updateQueueStatus($queueId, 'no_show', "Queue {$queue->queue_number} marked as no show!");
};

// Bulk actions
$selectAll = function () {
    $this->selectedQueues = $this->queues->pluck('id')->toArray();
    $this->showBulkActions = count($this->selectedQueues) > 0;
};

$selectQueue = function ($queueId) {
    if (in_array($queueId, $this->selectedQueues)) {
        $this->selectedQueues = array_diff($this->selectedQueues, [$queueId]);
    } else {
        $this->selectedQueues[] = $queueId;
    }
    $this->showBulkActions = count($this->selectedQueues) > 0;
};

$bulkComplete = function () {
    foreach ($this->selectedQueues as $queueId) {
        $this->complete($queueId);
    }
    $this->selectedQueues = [];
    $this->showBulkActions = false;
    session()->flash('success', 'Selected queues have been completed!');
};

$bulkCancel = function () {
    foreach ($this->selectedQueues as $queueId) {
        $this->cancel($queueId);
    }
    $this->selectedQueues = [];
    $this->showBulkActions = false;
    session()->flash('success', 'Selected queues have been cancelled!');
};

$clearSelection = function () {
    $this->selectedQueues = [];
    $this->showBulkActions = false;
};

?>

<section class="min-h-screen bg-gray-50/30 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Queue Management</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage patient queues and appointments</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('queue-display') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Queue Display
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Queue Controls -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by patient name, phone, or queue number..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Clinic Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Clinic</label>
                        <select wire:model.live="clinic_id"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Clinics</option>
                            @foreach ($this->clinics as $clinic)
                                <option value="{{ $clinic->clinic?->id ?? '' }}">{{ $clinic->clinic?->name ?? 'Unknown Clinic' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select wire:model.live="filter"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="waiting">Waiting</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            @if($showBulkActions)
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-indigo-800 dark:text-indigo-200">
                                {{ count($selectedQueues) }} queue(s) selected
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="bulkComplete" 
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Complete All
                            </button>
                            <button wire:click="bulkCancel" 
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel All
                            </button>
                            <button wire:click="clearSelection" 
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Clear Selection
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Queue List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" wire:click="selectAll" 
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Queue #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Patient
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Clinic
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Waiting Time
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($this->queues as $queue)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" wire:click="selectQueue({{ $queue->id }})" 
                                            @if(in_array($queue->id, $selectedQueues)) checked @endif
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">
                                                        {{ substr($queue->queue_number, -2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $queue->queue_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $queue->patient?->full_name ?? 'Unknown Patient' }}
                                        </div>
                                        @if($queue->patient?->phone)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $queue->patient->phone }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $queue->clinic?->name ?? 'Unknown Clinic' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityColors = [
                                                'emergency' => 'bg-red-500 text-white',
                                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                                'normal' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityColors[$queue->priority] ?? $priorityColors['normal'] }}">
                                            {{ ucfirst($queue->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'waiting' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                                'no_show' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$queue->status] ?? $statusColors['waiting'] }}">
                                            {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if ($queue->status === 'waiting')
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $queue->created_at->diffForHumans(null, true) }}
                                            </div>
                                        @elseif($queue->status === 'in_progress')
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $queue->called_at->diffForHumans(null, true) }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if ($queue->status === 'waiting')
                                                <button wire:click="callNext({{ $queue->id }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    Call
                                                </button>
                                            @elseif($queue->status === 'in_progress')
                                                <button wire:click="complete({{ $queue->id }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Complete
                                                </button>
                                            @endif
                                            
                                            @if ($queue->status === 'waiting')
                                                <button wire:click="noShow({{ $queue->id }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    No Show
                                                </button>
                                            @endif
                                            
                                            @if ($queue->status !== 'completed' && $queue->status !== 'cancelled')
                                                <button wire:click="cancel({{ $queue->id }})"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Cancel
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No queues found</h3>
                                            <p class="text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $this->queues->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
