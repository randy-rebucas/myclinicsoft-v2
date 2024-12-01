<?php

use Livewire\Volt\Component;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Receptionist;
use App\Events\QueueUpdated;
use function Livewire\Volt\{state, form, mount, computed, with};

state([
    'listeners' => ['echo:queues,QueueUpdated' => 'refreshQueues'],
    'search',
    'todayQueue' => [],
    'recentActivities' => [],
    'receptionist' => Receptionist::where('user_id', auth()->id())->first(),
]);

with(fn() => ['patients' => Patient::where('first_name', 'like', '%' . $this->search . '%')->paginate(10)]);

mount(function () {
    $this->refreshQueues();

    $this->recentActivities = $this->receptionist
        ?->activities()
        ->latest()
        ->take(10)
        ->get()
        ->map(function ($activity) {
            $typeConfig = match ($activity->type) {
                'appointment_created' => ['color' => 'blue', 'icon' => 'calendar'],
                'prescription_added' => ['color' => 'green', 'icon' => 'prescription'],
                'lab_result_added' => ['color' => 'purple', 'icon' => 'lab'],
                'note_added' => ['color' => 'yellow', 'icon' => 'note'],
                'billing_updated' => ['color' => 'red', 'icon' => 'billing'],
                'created' => ['color' => 'gray', 'icon' => 'note'],
                'updated' => ['color' => 'gray', 'icon' => 'note'],
                'deleted' => ['color' => 'gray', 'icon' => 'note'],
                default => ['color' => 'gray', 'icon' => 'note'],
            };

            return [
                'type' => $activity->type,
                'title' => ucfirst(str_replace('_', ' ', $activity->type)),
                'description' => $activity->description,
                'timestamp' => $activity->created_at->diffForHumans(),
                'color' => $typeConfig['color'],
                'icon' => $typeConfig['icon'],
            ];
        });
});

$refreshQueues = function () {
    $this->todayQueue = Queue::with('patient')->whereDate('created_at', now()->toDateString())->orderBy('created_at')->get();
};

$callNext = function ($queueId) {
    $queue = Queue::find($queueId);
    $queue->update([
        'status' => 'in_progress',
        'called_at' => now(),
    ]);

    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated("Queue {$queue->queue_number} is now in progress!", 'in_progress'))->toOthers();
};

$complete = function ($queueId) {
    $queue = Queue::find($queueId);
    $queue->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated("Queue {$queue->queue_number} is now completed!", 'completed'))->toOthers();
};

$cancel = function ($queueId) {
    $queue = Queue::find($queueId);
    $queue->update(['status' => 'cancelled']);
    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated("Queue {$queue->queue_number} has been cancelled!", 'cancelled'))->toOthers();
};

?>

<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <button
            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="ml-3 font-medium text-gray-900">Add Queue</span>
        </button>

        <button
            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-green-500 hover:bg-green-50 transition-all">
            <div class="p-2 bg-green-50 rounded-lg">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <span class="ml-3 font-medium text-gray-900">New Patient</span>
        </button>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 grid grid-cols-1 gap-6">
            <!-- Patients List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Patients List</h2>
                    <div class="relative">
                        <input type="text" wire:model.live="search" placeholder="Search patients..."
                            class="w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Patient items -->
                        @forelse ($patients as $patient)
                            <div
                                class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $patient->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($patient->full_name) }}"
                                            alt="{{ $patient->full_name }}">
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $patient->full_name }}</p>
                                        <p class="text-sm text-gray-500">ID: PAT-001 • Last Visit: 2 weeks ago</p>
                                    </div>
                                </div>
                                <button class="p-2 text-gray-400 hover:text-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-gray-500">No patients found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Activity Items -->
                        @forelse ($recentActivities as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-{{ $activity['color'] }}-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $activity['timestamp'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-gray-500">No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Queues -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Queues</h2>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700">Today's List</span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($todayQueue as $queue)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-lg font-semibold text-gray-900">{{ $queue->queue_number }}</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($queue->priority === 'urgent') bg-red-50 text-red-700
                                @elseif($queue->priority === 'medium') bg-yellow-50 text-yellow-700
                                @else bg-green-50 text-green-700 @endif">
                                {{ ucfirst($queue->priority) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <x-heroicon-m-user class="w-4 h-4" />
                                <span>{{ $queue->patient->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <x-heroicon-m-building-office class="w-4 h-4" />
                                <span>{{ $queue->department->name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-3">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                @switch($queue->status)
                                    @case('waiting')
                                        bg-yellow-50 text-yellow-700
                                        @break
                                    @case('in_progress')
                                        bg-blue-50 text-blue-700
                                        @break
                                    @case('completed')
                                        bg-green-50 text-green-700
                                        @break
                                    @case('cancelled')
                                        bg-red-50 text-red-700
                                        @break
                                    @default
                                        bg-gray-50 text-gray-700
                                @endswitch">
                                {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                            </span>

                            @if($queue->status === 'waiting')
                                <button wire:click="callNext({{ $queue->id }})"
                                    class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-50 rounded-full hover:bg-blue-100">
                                    Start
                                </button>
                            @elseif($queue->status === 'in_progress')
                                <button wire:click="complete({{ $queue->id }})"
                                    class="px-3 py-1 text-xs font-medium text-green-700 bg-green-50 rounded-full hover:bg-green-100">
                                    Complete
                                </button>
                            @endif

                            @if($queue->status !== 'completed' && $queue->status !== 'cancelled')
                                <button wire:click="cancel({{ $queue->id }})"
                                    class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded-full hover:bg-gray-100">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        <x-heroicon-o-queue-list class="w-8 h-8 mx-auto mb-2" />
                        <p>No queues available</p>
                    </div>
                @endforelse
            </div>

            @if($todayQueue->count() > 0)
                <div class="p-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Total Queues: {{ $todayQueue->count() }}</span>
                        <button class="text-blue-600 hover:text-blue-700">View All →</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
