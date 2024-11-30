<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

?>
<div class="space-y-6">
    <!-- Queue Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                <select wire:model.live="department_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select wire:model.live="filter" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="waiting">Waiting</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Queue List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Queue #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waiting Time</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($queues as $queue)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $queue->queue_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $queue->patient->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $queue->department->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $queue->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                       ($queue->priority === 'emergency' ? 'bg-red-500 text-white' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                    {{ ucfirst($queue->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $queue->status === 'waiting' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                       ($queue->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       ($queue->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200')) }}">
                                    {{ ucfirst($queue->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($queue->status === 'waiting')
                                    {{ $queue->created_at->diffForHumans(null, true) }}
                                @elseif($queue->status === 'in_progress')
                                    {{ $queue->called_at->diffForHumans(null, true) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if($queue->status === 'waiting')
                                    <button wire:click="callNext({{ $queue->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                        Call
                                    </button>
                                @elseif($queue->status === 'in_progress')
                                    <button wire:click="complete({{ $queue->id }})" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                        Complete
                                    </button>
                                @endif
                                @if($queue->status !== 'completed' && $queue->status !== 'cancelled')
                                    <button wire:click="cancel({{ $queue->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        Cancel
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $queues->links() }}
        </div>
    </div>
</div>
