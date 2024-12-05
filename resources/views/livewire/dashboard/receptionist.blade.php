<?php

use Livewire\Volt\Component;
use App\Models\Patient;
use App\Models\PatientDoctor;
use App\Models\ClinicDoctor;
use App\Models\Queue;
use App\Models\Vital;
use App\Models\Receptionist;
use App\Models\Department;
use App\Events\QueueUpdated;
use App\Livewire\Forms\QueueForm;
use Carbon\Carbon;
use function Livewire\Volt\{state, form, mount, computed, with, usesPagination, on};

usesPagination();

form(QueueForm::class);

state([
    'search',
    'recentActivities' => [],
    'receptionist' => Receptionist::where('user_id', auth()->id())->first(),
    'showPreviewModal' => false,
    'selectedPatient' => null,
    'isEditing' => false,
    'editableValue' => '',
    'model' => null,
    'field' => null,
    'genders' => fn() => [
        'male' => 'Male',
        'female' => 'Female',
        'unknown' => 'Unknown',
    ],
    'filter' => 'all',
    'clinic_id' => null,
    // 'listeners' => ['echo:queues,QueueUpdated' => 'refreshQueues'],
]);

with(
    fn() => [
        'patients' => PatientDoctor::with('patient')
            ->whereHas('patient', function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%');
            })
            ->where('doctor_id', auth()->user()->receptionist->doctor->id)
            ->orderBy('created_at', 'asc')
            ->paginate(10),
    ],
);

mount(function () {
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

    $this->clinic_id = ClinicDoctor::with('clinic')
        ->where('is_primary', true)
        ->where('doctor_id', auth()->user()->receptionist->doctor->id)
        ->first()->clinic->id;
});

$todayQueue = computed(function () {
    return Queue::with(['patient', 'clinic'])
        ->when($this->filter !== 'all', fn($query) => $query->where('status', $this->filter))
        ->when($this->clinic_id, fn($query) => $query->where('clinic_id', $this->clinic_id))
        ->whereDate('created_at', Carbon::today())
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'asc')
        ->get();
});

$refreshQueues = function () {
    $this->dispatch('$refresh');
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

$openCreatePatientModal = function () {
    $this->dispatch('open-create-patient-modal');
};

$preview = function ($patientId) {
    $this->selectedPatient = Patient::find($patientId);
    $this->form->clinic_id = $this->clinic_id;
    $this->form->patient_id = $this->selectedPatient->id;
    $this->form->priority = 'normal';

    $this->dispatch('open-preview-modal');
};

$closeModal = function () {
    $this->selectedPatient = null;
    $this->dispatch('close-preview-modal');
};

$createQueue = function () {
    $this->form->store();
    $this->dispatch('close-preview-modal');
    $this->search = '';
};

$startEditing = function ($value, $field, $model) {
    $this->isEditing = true;
    $this->editableValue = $value;
    $this->field = $field;
    $this->model = $model;
};

$save = function () {
    $this->validate([
        'editableValue' => 'required',
    ]);

    if ($this->model === 'vitals') {
        Vital::updateOrCreate(['patient_id' => $this->selectedPatient->id], [$this->field => $this->editableValue]);
    } else {
        $this->selectedPatient->update([$this->field => $this->editableValue]);
    }
    $this->isEditing = false;

    $this->dispatch('valueUpdated');
};

$valueUpdated = function () {
    $this->dispatch('refresh');
};

$cancelEdit = function () {
    $this->isEditing = false;
    $this->model = null;
    $this->field = null;
    $this->editableValue = '';
};

on([
    'set-patient' => function ($patientId) {
        $this->search = Patient::find($patientId)->first_name;
        $this->preview($patientId);
    },
    'echo:queues,QueueUpdated' => $refreshQueues,
]);
?>

<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <button wire:click="openCreatePatientModal"
            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-green-500 hover:bg-green-50 transition-all">
            <div class="p-2 bg-green-50 rounded-lg">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div class="ml-3">
                <span class="font-medium text-gray-900">New Patient</span>
                <p class="text-sm text-gray-500">{{ $patients->total() }} total</p>
            </div>
        </button>

        <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="ml-3">
                <span class="font-medium text-gray-900">Current Queue</span>
                {{-- <p class="text-sm text-gray-500">{{ $this->todayQueue->count() }} today</p> --}}
            </div>
        </div>

        <!-- Now Serving Section -->
        <div class="col-span-2 flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-2 bg-yellow-50 rounded-lg">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="ml-3">
                <span class="font-medium text-gray-900">Now Serving</span>
                @php
                    $currentQueue = $this->todayQueue
                        ->where('clinic_id', $this->clinic_id)
                        ->where('status', 'in_progress')
                        ->first();
                @endphp
                @if ($currentQueue)
                    <p class="text-lg font-bold text-yellow-500">{{ $currentQueue->queue_number }}</p>
                @else
                    <p class="text-sm text-gray-500">No active queue</p>
                @endif
            </div>
        </div>
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
                        @forelse ($patients as $item)
                            <div
                                class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $item->patient->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->patient->full_name) }}"
                                            alt="{{ $item->patient->full_name }}">
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->patient->full_name }}</p>
                                        <div class="text-sm text-gray-500 truncate">
                                            {{ strtoupper($item->patient->gender) }}
                                            @if ($item->patient->date_of_birth)
                                                • {{ $item->patient->age }} years
                                                • Born {{ $item->patient->date_of_birth->format('M d, Y') }}
                                            @endif
                                            @if ($item->patient->height || $item->patient->weight)
                                                •
                                                {{ $item->patient->height ? 'H: ' . $item->patient->height . 'cm' : '' }}{{ $item->patient->height && $item->patient->weight ? ' / ' : '' }}{{ $item->patient->weight ? 'W: ' . $item->patient->weight . 'kg' : '' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="preview({{ $item->patient->id }})"
                                    class="p-2 text-gray-400 hover:text-blue-500">
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
                {{-- <div>
                    {{ $patients->links() }}
                </div> --}}
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
            <div class="p-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Queues</h2>
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700">Today's
                        List</span>
                </div>
            </div>

            <div class="overflow-y-auto flex-1">
                <div class="flex-1 overflow-y-auto">
                    @forelse($this->todayQueue as $queue)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-lg font-semibold text-gray-900">{{ $queue->queue_number }}</span>
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full
                                    @if ($queue->priority === 'urgent') bg-red-50 text-red-700
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
                                    <span>{{ $queue->clinic->name }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-3">
                                <span
                                    class="px-2.5 py-1 text-xs font-medium rounded-full
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

                                @if ($queue->status === 'waiting')
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

                                @if ($queue->status !== 'completed' && $queue->status !== 'cancelled')
                                    <button wire:click="cancel({{ $queue->id }})"
                                        class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded-full hover:bg-gray-100">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="align-middle flex flex-col h-screen justify-center p-4 text-center text-gray-500">
                            <x-heroicon-o-queue-list class="w-8 h-8 mx-auto mb-2" />
                            <p>No queues available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if ($this->todayQueue->count() > 0)
                <div class="p-4 border-t border-gray-100 flex-shrink-0">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Total Queues: {{ $this->todayQueue->count() }}</span>
                        <button class="text-blue-600 hover:text-blue-700">View All →</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Wrap both modal and backdrop in a parent div with x-data -->
    <div x-data="{ show: false }" @open-preview-modal.window="show = true" @close-preview-modal.window="show = false">

        <!-- Patient Preview Modal -->
        <div x-show="show" x-on:keydown.escape.window="show = false"
            x-transition:enter="transform transition ease-in-out duration-500"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-500"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-50">

            @if ($selectedPatient)
                @php
                    $vital = $selectedPatient->vitals->last();
                @endphp
                <div class="h-full flex flex-col">
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Patient Details</h3>
                        <button @click="show = false; $wire.closeModal()" class="text-gray-400 hover:text-gray-500">
                            <x-heroicon-m-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="space-y-6">
                            <!-- Patient Photo & Basic Info -->
                            <div class="text-center">
                                <img class="h-24 w-24 rounded-full mx-auto"
                                    src="{{ $selectedPatient->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($selectedPatient->full_name) }}"
                                    alt="{{ $selectedPatient->full_name }}">
                                <h4 class="mt-2 text-xl font-medium text-gray-900">{{ $selectedPatient->full_name }}
                                </h4>
                                <p class="text-sm text-gray-500">Patient ID: {{ $selectedPatient->id }}</p>
                            </div>

                            <!-- Patient Details -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                                        <p class="text-gray-900">
                                            {{ $selectedPatient->date_of_birth?->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Gender</label>
                                        <p class="text-gray-900">{{ ucfirst($selectedPatient->gender) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Phone</label>
                                        <p class="text-gray-900">{{ $selectedPatient->phone ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Email</label>
                                        <p class="text-gray-900">{{ $selectedPatient->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Address</label>
                                    <p class="text-gray-900">{{ $selectedPatient->address ?? '-' }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex items-end justify-between relative">
                                        @if ($isEditing && $field === 'height')
                                            <div class="flex items-center space-x-2 w-full">
                                                <input type="text" wire:model="editableValue"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Enter height">
                                                <button wire:click="save"
                                                    class="inline-flex items-center p-1 text-green-600 hover:text-green-700">
                                                    <x-heroicon-m-check class="w-5 h-5" />
                                                </button>
                                                <button wire:click="cancelEdit"
                                                    class="inline-flex items-center p-1 text-red-600 hover:text-red-700">
                                                    <x-heroicon-m-x-mark class="w-5 h-5" />
                                                </button>
                                            </div>
                                        @else
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Height</label>
                                                <p class="text-gray-900">{{ $selectedPatient->height ?? '-' }} cm</p>
                                            </div>
                                            <button
                                                wire:click="startEditing('{{ $selectedPatient->height }}', 'height', 'patient')"
                                                class="ml-2 text-blue-600 hover:text-blue-500">
                                                <x-heroicon-m-pencil class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex items-end justify-between relative">
                                        @if ($isEditing && $field === 'weight')
                                            <div class="flex items-center space-x-2 w-full">
                                                <input type="text" wire:model="editableValue"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Enter weight">
                                                <button wire:click="save"
                                                    class="inline-flex items-center p-1 text-green-600 hover:text-green-700">
                                                    <x-heroicon-m-check class="w-5 h-5" />
                                                </button>
                                                <button wire:click="cancelEdit"
                                                    class="inline-flex items-center p-1 text-red-600 hover:text-red-700">
                                                    <x-heroicon-m-x-mark class="w-5 h-5" />
                                                </button>
                                            </div>
                                        @else
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Weight</label>
                                                <p class="text-gray-900">{{ $selectedPatient->weight ?? '-' }} kg</p>
                                            </div>
                                            <button
                                                wire:click="startEditing('{{ $selectedPatient->weight }}', 'weight', 'patient')"
                                                class="ml-2 text-blue-600 hover:text-blue-500">
                                                <x-heroicon-m-pencil class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex items-end justify-between relative">
                                        @if ($isEditing && $field === 'temperature')
                                            <div class="flex items-center space-x-2 w-full">
                                                <input type="text" wire:model="editableValue"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Enter temperature">
                                                <button wire:click="save"
                                                    class="inline-flex items-center p-1 text-green-600 hover:text-green-700">
                                                    <x-heroicon-m-check class="w-5 h-5" />
                                                </button>
                                                <button wire:click="cancelEdit"
                                                    class="inline-flex items-center p-1 text-red-600 hover:text-red-700">
                                                    <x-heroicon-m-x-mark class="w-5 h-5" />
                                                </button>
                                            </div>
                                        @else
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Temperature</label>
                                                <p class="text-gray-900">{{ $vital ? $vital->temperature : '-' }} °C
                                                </p>
                                            </div>
                                            <button
                                                wire:click="startEditing('{{ $vital ? $vital->temperature : '' }}', 'temperature', 'vitals')"
                                                class="ml-2 text-blue-600 hover:text-blue-500">
                                                <x-heroicon-m-pencil class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex items-end justify-between relative">
                                        @if ($isEditing && $field === 'respiratory_rate')
                                            <div class="flex items-center space-x-2 w-full">
                                                <input type="text" wire:model="editableValue"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                    placeholder="Enter heart rate">
                                                <button wire:click="save"
                                                    class="inline-flex items-center p-1 text-green-600 hover:text-green-700">
                                                    <x-heroicon-m-check class="w-5 h-5" />
                                                </button>
                                                <button wire:click="cancelEdit"
                                                    class="inline-flex items-center p-1 text-red-600 hover:text-red-700">
                                                    <x-heroicon-m-x-mark class="w-5 h-5" />
                                                </button>
                                            </div>
                                        @else
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Heart Rate</label>
                                                <p class="text-gray-900">{{ $vital ? $vital->respiratory_rate : '-' }}
                                                    bpm</p>
                                            </div>
                                            <button
                                                wire:click="startEditing('{{ $vital ? $vital->respiratory_rate : '' }}', 'respiratory_rate', 'vitals')"
                                                class="ml-2 text-blue-600 hover:text-blue-500">
                                                <x-heroicon-m-pencil class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <form wire:submit="createQueue" class="p-6 border-t border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Add to Queue
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                <select wire:model.live="form.priority" name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4">
                            <x-primary-button class="w-full justify-center">
                                {{ __('Add to que') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Modal Backdrop -->
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
            @click="show = false; $wire.closeModal()">
        </div>
    </div>

    <div x-data="{ show: false }" @open-create-patient-modal.window="show = true"
        @close-create-patient-modal.window="show = false">

        <!-- Patient Preview Modal -->
        <div x-show="show" x-on:keydown.escape.window="show = false"
            x-transition:enter="transform transition ease-in-out duration-500"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-500"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl z-50">

            <div class="h-full flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Create New Patient</h3>
                    <button @click="show = false; $wire.closeModal()" class="text-gray-400 hover:text-gray-500">
                        <x-heroicon-m-x-mark class="w-5 h-5" />
                    </button>
                </div>
                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="space-y-6">
                        <livewire:patient.form />
                    </div>
                </div>
            </div>
        </div>
        <div x-show="show" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
            @click="show = false; $wire.closeModal()">
        </div>
    </div>
