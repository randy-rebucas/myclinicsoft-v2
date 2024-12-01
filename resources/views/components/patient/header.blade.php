@props([
    'patient',
    'onBack',
    'onEdit',
    'onNewEncounter'
])

<div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <!-- Back Button and Patient Name -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <button
                    type="button"
                    wire:click="$dispatch('{{ $this->onBack }}')"
                    class="mr-4 text-gray-400 hover:text-gray-500"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $patient->full_name }}
                </h1>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3">
                <button
                    type="button"
                    wire:click="$dispatch('{{ $onEdit }}')"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>

                <button
                    type="button"
                    wire:click="$dispatch('{{ $onNewEncounter }}')"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Encounter
                </button>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Age</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $patient->age }} years</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $patient->gender }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $patient->phone }}</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $patient->email }}</dd>
            </div>
        </div>
    </div>
</div>
