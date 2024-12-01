<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{state, mount};
use App\Models\Patient;

state([
    'activeTab' => 'overview',
    'tabs' => [
        'overview' => 'Overview',
        'personal' => 'Personal Info',
        'medical' => 'Medical Details',
        'insurance' => 'Insurance',
    ],
    'patient' => Patient::where('user_id', auth()->id())->first(),
    'recentActivities' => fn() => $this->patient
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
        }),
    'isEditingEmail' => false,
    'editableEmail' => '',
]);

$startEditingEmail = function () {
    $this->isEditingEmail = true;
    $this->editableEmail = $this->patient->user->email;
};

$saveEmail = function () {
    $this->validate([
        'editableEmail' => 'required|email',
    ]);

    $this->patient->user->update(['email' => $this->editableEmail]);
    $this->isEditingEmail = false;

    $this->dispatch('emailUpdated');
};

$emailUpdated = function () {
    $this->dispatch('refresh');
};

$cancelEditEmail = function () {
    $this->isEditingEmail = false;
    $this->editableEmail = '';
};
?>

<div class="space-y-6">
    @if ($patient)
        <!-- Patient Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6">
                <div class="flex items-center space-x-6">
                    <div class="relative">
                        <div class="h-20 w-20 rounded-full bg-gray-100 flex items-center justify-center">
                            @if ($patient->avatar)
                                <img src="{{ Storage::url($patient->avatar) }}" alt="{{ $patient->full_name }}"
                                    class="w-full h-full object-cover rounded-full">
                            @else
                                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 h-6 w-6 bg-green-500 rounded-full border-4 border-white">
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $patient->full_name }}</h1>
                                <div class="mt-1 flex items-center space-x-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        ID: #{{ $patient->id }}
                                    </span>
                                    <span class="text-gray-500">•</span>
                                    <span class="text-gray-600">{{ $patient->age }} years old</span>
                                    <span class="text-gray-500">•</span>
                                    <span class="text-gray-600">{{ $patient->gender }}</span>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-medium hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Edit
                                </button>
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Book Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="px-6 border-t border-gray-100">
                <nav class="-mb-px flex space-x-8">
                    @foreach ($tabs as $key => $label)
                        <button wire:click="$set('activeTab', '{{ $key }}')"
                            class="py-4 px-1 inline-flex items-center border-b-2 {{ $activeTab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No patient profile found. Please contact support or your administrator.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <!-- Overview Tab -->
        <div x-show="$wire.activeTab === 'overview'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Quick Stats -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Blood Type</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">O+</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Last Visit</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">Mar 15, 2024</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm font-medium text-gray-500">Next Appointment</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">Apr 3, 2024</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="border-t border-gray-100 pt-6">
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
                                        <div class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</div>
                                        <div class="mt-1 text-sm text-gray-500">{{ $activity['description'] }}</div>
                                        <div class="mt-1 text-sm text-gray-500">{{ $activity['timestamp'] }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Personal Info Tab -->
        <div x-show="$wire.activeTab === 'personal'" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <h3 class="font-medium text-gray-900 mb-4">Contact Details</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 flex items-center">
                            @if ($isEditingEmail)
                                <div class="flex items-center space-x-2">
                                    <input type="email" wire:model="editableEmail"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Enter email">
                                    <button wire:click="saveEmail"
                                        class="inline-flex items-center p-1 text-green-600 hover:text-green-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="cancelEditEmail"
                                        class="inline-flex items-center p-1 text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="text-gray-900">{{ $patient->user->email }}</span>
                                <button wire:click="startEditingEmail" class="ml-2 text-blue-600 hover:text-blue-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-gray-900">(555) 123-4567</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-gray-900">
                            123 Main Street<br>
                            Apt 4B<br>
                            New York, NY 10001
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="font-medium text-gray-900 mb-4">Emergency Contact</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-gray-900">Jane Doe</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Relationship</dt>
                        <dd class="mt-1 text-gray-900">Spouse</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-gray-900">(555) 987-6543</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Add Medical and Insurance tabs content similarly -->
    </div>
</div>
