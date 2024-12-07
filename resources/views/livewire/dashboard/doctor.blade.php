<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Patient;
use App\Models\PatientDoctor;
use App\Models\Queue;
use App\Models\Doctor;
use App\Models\Encounter;
use App\Events\QueueUpdated;
use function Livewire\Volt\{state, form, mount, computed, with, on};

state([
    'stats' => [],
    'queue' => null,
    'recentVisits' => [],
    'todayQueue' => [],
    'recentPatients' => [],
    'doctor' => Doctor::where('user_id', auth()->id())->first(),
    'recentActivities' => [],
]);

with(['stats', 'recentVisits', 'todayQueue', 'recentPatients']);

mount(function () {
    $this->stats = [
        [
            'label' => 'Total Patients',
            'value' => PatientDoctor::where('doctor_id', auth()->user()->doctor->id)->count(),
            'icon' => 'heroicon-o-users',
        ],
        [
            'label' => 'New Patients',
            'value' => PatientDoctor::whereMonth('created_at', now()->month)
                ->where('doctor_id', auth()->user()->doctor->id)
                ->count(),
            'icon' => 'heroicon-o-user-plus',
        ],
        [
            'label' => 'Total Visits',
            'value' => Encounter::where('doctor_id', auth()->user()->doctor->id)->count(),
            'icon' => 'heroicon-o-clipboard',
        ],
        [
            'label' => 'This Month',
            'value' => Encounter::whereMonth('created_at', now()->month)
                ->where('doctor_id', auth()->user()->doctor->id)
                ->count(),
            'icon' => 'heroicon-o-calendar',
        ],
    ];
    $this->recentVisits = Encounter::with('patient')
        ->where('doctor_id', auth()->user()->doctor->id)
        ->latest()
        ->take(8)
        ->get();
    $this->todayQueue = Queue::with('patient')
        ->whereDate('created_at', now()->toDateString())
        ->where('clinic_id', auth()->user()->doctor->clinics->first()->id)
        ->orderBy('created_at')
        ->get();
    $this->recentPatients = PatientDoctor::with('patient')
        ->where('doctor_id', auth()->user()->doctor->id)
        ->latest()
        ->take(8)
        ->get();
    $this->recentActivities = $this->doctor
        ?->activities()
        ->latest()
        ->take(5)
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
    $this->todayQueue = Queue::with('patient')
        ->whereDate('created_at', now()->toDateString())
        ->where('clinic_id', auth()->user()->doctor->clinics->first()->id)
        ->orderBy('created_at')
        ->get();
};

$visit = function (Queue $visit) {
    $this->queue = $visit;
    $visit->update([
        'status' => 'in_progress',
        'called_at' => now(),
    ]);

    $this->dispatch('queue-updated');
    broadcast(new QueueUpdated("Queue {$visit->queue_number} is now in progress!", 'in_progress'))->toOthers();
};

on([
    'queue-completed' => function ($patientId) {
        $this->queue = null;
    },
    'echo:queues,QueueUpdated' => $refreshQueues,
]);
?>

<div class="bg-gray-50 min-h-screen py-8">
    @if ($queue)
        <livewire:patient.record :queue="$queue" />
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Stats Cards - Now with gradients and improved visual hierarchy -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach ($stats as $stat)
                    <div
                        class="relative group overflow-hidden bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="p-6 relative">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-600">{{ $stat['label'] }}</div>
                                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $stat['value'] }}</div>
                                </div>
                                <div class="p-3 bg-indigo-100 rounded-xl">
                                    @svg($stat['icon'], 'w-7 h-7 text-indigo-600')
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Today's Queue -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Today's Queue</h3>
                            <span
                                class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-full">{{ $todayQueue->count() }}
                                Patients</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse($todayQueue as $visit)
                                <div class="cursor-pointer flex gap-4 group hover:bg-gray-50 items-center py-4 rounded-lg transition-colors"
                                    wire:click="visit({{ $visit }})">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-semibold">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">{{ $visit->patient->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center gap-2">
                                            @svg('heroicon-o-clock', 'w-4 h-4')
                                            {{ $visit->created_at?->format('h:ia') ?? 'Unscheduled' }}
                                        </p>
                                    </div>
                                    <div @class([
                                        'px-3 py-1 text-xs font-medium rounded-full',
                                        'bg-yellow-100 text-yellow-800' => $visit->status === 'waiting',
                                        'bg-blue-100 text-blue-800' => $visit->status === 'in_progress',
                                        'bg-green-100 text-green-800' => $visit->status === 'completed',
                                        'bg-gray-100 text-gray-800' => !in_array($visit->status, [
                                            'waiting',
                                            'in_progress',
                                            'completed',
                                        ]),
                                    ])>
                                        {{ str($visit->status)->replace('_', ' ')->title() }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center">
                                    <div
                                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        @svg('heroicon-o-queue-list', 'w-8 h-8 text-gray-400')
                                    </div>
                                    <p class="text-gray-500">No visits scheduled for today</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Activity Timeline -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                View All
                            </button>
                        </div>

                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach ($recentActivities as $activity)
                                    <li class="relative pb-8">
                                        @if (!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span
                                                    class="h-8 w-8 rounded-full {{ 'bg-' . $activity['color'] . '-500' }} flex items-center justify-center ring-8 ring-white">
                                                    @switch($activity['icon'])
                                                        @case('prescription')
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                        @break

                                                        @case('calendar')
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        @break

                                                        @case('lab')
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                            </svg>
                                                        @break

                                                        @case('note')
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" />
                                                            </svg>
                                                        @break

                                                        @case('billing')
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" />
                                                            </svg>
                                                        @break
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900">{{ $activity['title'] }}
                                                </div>
                                                <div class="mt-1 text-sm text-gray-500">{{ $activity['description'] }}
                                                </div>
                                                <div class="mt-1 text-sm text-gray-500">{{ $activity['timestamp'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Recent Visits & Patients -->
                <div class="space-y-6">
                    <!-- Recent Visits Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Visits</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                View All
                            </button>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($recentVisits as $visit)
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

                    <!-- Recent Patients Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Patients</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                View All
                            </button>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($recentPatients as $item)
                                <div class="py-4 flex items-center space-x-4">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-green-500"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $item->patient->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Added {{ $item->patient->user->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $item->patient->phone }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-gray-500 text-sm">No recent patients</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
