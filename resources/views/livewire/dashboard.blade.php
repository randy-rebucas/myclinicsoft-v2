<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Encounter;
use function Livewire\Volt\{state, layout, form, mount, computed, with};

state([
    'stats' => [],
    'recentActivity' => [],
    'todayQueue' => [],
    'listeners' => ['echo:queues,QueueUpdated' => 'refreshQueues'],
]);

layout('layouts.app');

mount(function () {
    $this->stats = [
        [
            'label' => 'Total Patients',
            'value' => Patient::count(),
            'icon' => 'heroicon-o-users',
        ],
        [
            'label' => 'New Patients',
            'value' => Patient::whereMonth('created_at', now()->month)->count(),
            'icon' => 'heroicon-o-user-plus',
        ],
        [
            'label' => 'Total Visits',
            'value' => Encounter::count(),
            'icon' => 'heroicon-o-clipboard',
        ],
        [
            'label' => 'This Month',
            'value' => Encounter::whereMonth('created_at', now()->month)->count(),
            'icon' => 'heroicon-o-calendar',
        ],
    ];
    $this->recentActivity = Encounter::with('patient')->latest()->take(8)->get();
    $this->todayQueue = Queue::with('patient')->whereDate('created_at', now()->toDateString())->orderBy('created_at')->get();
});

function refreshQueues() {
    $this->todayQueue = Queue::with('patient')->whereDate('created_at', now()->toDateString())->orderBy('created_at')->get();
}
?>

<section>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($stats as $stat)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">
                                        {{ $stat['label'] }}
                                    </div>
                                    <div class="mt-1 text-3xl font-semibold text-gray-900">
                                        {{ $stat['value'] }}
                                    </div>
                                </div>
                                <div class="p-3 bg-indigo-50 rounded-full">
                                    @svg($stat['icon'], 'w-6 h-6 text-indigo-600')
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Latest Visits Queue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Visits</h3>
                        <div class="divide-y divide-gray-200">
                            @forelse($recentActivity as $visit)
                                <div class="py-4 flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $visit->patient->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $visit->encounter_date->format('M d, Y h:ia') }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $visit->reason }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-gray-500 text-sm">No recent visits</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Today's Queue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Queue</h3>
                        <div class="divide-y divide-gray-200">
                            @forelse($todayQueue as $visit)
                                <div class="py-4 flex items-center space-x-4">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 text-blue-500 font-medium">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $visit->patient->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $visit->created_at?->format('h:ia') ?? 'Unscheduled' }}
                                        </p>
                                    </div>
                                    <div @class([
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        'bg-yellow-100 text-yellow-800' => $visit->status === 'waiting',
                                        'bg-blue-100 text-blue-800' => $visit->status === 'in_progress',
                                        'bg-green-100 text-green-800' => $visit->status === 'completed',
                                        'bg-gray-100 text-gray-800' => !in_array($visit->status, ['waiting', 'in_progress', 'completed']),
                                    ])>
                                        {{ str($visit->status)->replace('_', ' ')->title() }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-gray-500 text-sm">No visits scheduled for today</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
