<?php

use App\Models\Patient;
use App\Models\Queue;
use App\Models\Department;
use function Livewire\Volt\{layout, form, computed, state, on, mount, title};
use App\Livewire\Forms\QueueForm;
use Illuminate\Support\Str;

state(['patientId'])->url();

form(QueueForm::class);

state([
    'patient' => fn() => Patient::find($this->patientId),
    'activeTab' => 'overview',
]);

layout('layouts.app');

title(fn() => 'Patient: ' . $this->patient->full_name);

mount(function () {
    $this->form->queue_number = $this->generateSequenceNumber('queing', [], 'SQ-', 3);
    $this->form->department_id = 1; // update this to be dynamic
    $this->form->patient_id = $this->patient->id;
    $this->form->priority = 'normal';
});

$departments = computed(function () {
    return Department::active()->get();
});

$que = computed(function () {
    return Queue::where('patient_id', $this->patient->id)
        ->where('status', '!=', 'completed')
        ->whereDate('created_at', now()->toDateString()) // Add this line to filter by current date
        ->latest()
        ->first();
});

$update = function (Queue $que, $status) {
    $que->status = $status;
    $que->save();

    $this->dispatch('refresh');
    $this->dispatch('encounter');
};

$generateSequenceNumber = function ($tablename, array $conditions = [], string $prefix, int $length = 5) {
    $model = DB::table($tablename);

    if (is_array($conditions) && count($conditions) > 0) {
        $model = $model->where($conditions);
    }

    return $prefix . str_pad($model->count() + 1, $length, '0', STR_PAD_LEFT);
};

$add = function () {
    $this->dispatch('open-modal', 'add-to-queue');
};

$create = function () {
    $this->form->store();

    $this->dispatch('close-modal', 'add-to-queue');

    $this->dispatch('refresh');
};
?>

<div>
    <!-- Patient Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                    <span class="text-indigo-600 dark:text-indigo-300 text-base font-semibold">
                        {{ substr($patient->full_name, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $patient->full_name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Patient ID: #{{ $patient->id }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if ($this->que)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Queue Status:</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $this->que->status === 'waiting' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $this->que->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $this->que->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ Str::headline($this->que->status) }}
                        </span>
                    </div>
                @else
                    <x-primary-button wire:click="add" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Add to Queue') }}</span>
                    </x-primary-button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
            <!-- Enhanced Tab Navigation -->
            <div class="border-b border-gray-100 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button wire:click="$set('activeTab', 'overview')" @class([
                        'group relative min-w-0 flex-1 overflow-hidden py-4 px-1 text-center text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-10 focus:outline-none',
                        'text-indigo-600 dark:text-indigo-400' => $activeTab === 'overview',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' =>
                            $activeTab !== 'overview',
                    ])>
                        <span>{{ __('Overview') }}</span>
                        <span aria-hidden="true" @class([
                            'absolute inset-x-0 bottom-0 h-0.5',
                            'bg-indigo-600 dark:bg-indigo-400' => $activeTab === 'overview',
                            'bg-transparent' => $activeTab !== 'overview',
                        ])></span>
                    </button>

                    <button wire:click="$set('activeTab', 'encounters')" @class([
                        'group relative min-w-0 flex-1 overflow-hidden py-4 px-1 text-center text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-10 focus:outline-none',
                        'text-indigo-600 dark:text-indigo-400' => $activeTab === 'encounters',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' =>
                            $activeTab !== 'encounters',
                    ])>
                        <span>{{ __('Encounters') }}</span>
                        <span aria-hidden="true" @class([
                            'absolute inset-x-0 bottom-0 h-0.5',
                            'bg-indigo-600 dark:bg-indigo-400' => $activeTab === 'encounters',
                            'bg-transparent' => $activeTab !== 'encounters',
                        ])></span>
                    </button>

                    <button wire:click="$set('activeTab', 'vitals')" @class([
                        'group relative min-w-0 flex-1 overflow-hidden py-4 px-1 text-center text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-10 focus:outline-none',
                        'text-indigo-600 dark:text-indigo-400' => $activeTab === 'vitals',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' =>
                            $activeTab !== 'vitals',
                    ])>
                        <span>{{ __('Vital Signs') }}</span>
                        <span aria-hidden="true" @class([
                            'absolute inset-x-0 bottom-0 h-0.5',
                            'bg-indigo-600 dark:bg-indigo-400' => $activeTab === 'vitals',
                            'bg-transparent' => $activeTab !== 'vitals',
                        ])></span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content with Smooth Transition -->
            <div class="p-6">
                @if ($activeTab === 'overview')
                    <div class="space-y-6">
                        <!-- Detailed Information Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Personal Information -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information
                                    </h3>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->full_name }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->date_of_birth?->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Age</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->date_of_birth?->age }} years
                                                </p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Gender</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::title($patient->gender) ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Blood Type</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->blood_type ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Marital Status</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->marital_status ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Occupation</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->occupation ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">National ID</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->national_id ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Nationality</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->nationality ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Language</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->preferred_language ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Information
                                    </h3>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Primary Phone</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->phone ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Secondary Phone</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->secondary_phone ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->email ?? '-' }}</p>
                                        </div>
                                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Emergency
                                                Contact</p>
                                            <div class="space-y-2">
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->emergency_contact_name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Relationship
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->emergency_contact_relationship ?? '-' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->emergency_contact_phone ?? '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->address ?? '-' }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">City</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->city ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">State/Province</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->state ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Postal Code</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->postal_code ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Country</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $patient->country ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Medical Information
                                    </h3>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Insurance Provider</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->insurance_provider ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Insurance ID</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->insurance_id ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Primary Care Physician
                                            </p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $patient->primary_physician ?: '-' }}</p>
                                        </div>
                                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Medical
                                                History</p>
                                            <div class="space-y-2">
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Allergies</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->allergies ?: 'None reported' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Chronic
                                                        Conditions</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->chronic_conditions ?: 'None reported' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Current
                                                        Medications</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->current_medications ?: 'None reported' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                Additional Information</p>
                                            <div class="space-y-2">
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Height</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->height ?? '-' }} cm
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Weight</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->weight ?? '-' }} kg
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Past Surgeries
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->past_surgeries ?: 'None reported' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Family History
                                                    </p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $patient->family_history ?: 'None reported' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($activeTab === 'encounters')
                    <div class="bg-white dark:bg-gray-800 rounded-lg">
                        <livewire:patient.encounter.index :patient="$patient" />
                    </div>
                @elseif ($activeTab === 'vitals')
                    <div class="bg-white dark:bg-gray-800 rounded-lg">
                        <livewire:patient.record.vital-sign :patient="$patient" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="add-to-queue" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Add to Queue
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                    <select wire:model="form.department_id" name="department_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Select Department</option>
                        @foreach ($this->departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('form.department_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select wire:model="form.priority" name="priority"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea wire:model="form.notes" name="notes" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Add to que') }}
                </x-primary-button>
            </div>
        </form>

    </x-modal>
</div>
