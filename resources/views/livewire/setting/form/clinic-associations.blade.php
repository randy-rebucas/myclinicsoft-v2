
<section x-data>
    <x-card>
        <x-slot name="header">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Clinic Associations') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('View and manage your clinic associations. Contact an administrator to join or leave clinics.') }}
            </p>
        </x-slot>

        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="fixed bottom-4 right-4 z-50 max-w-sm w-full">
                <div class="rounded-lg shadow-lg bg-emerald-500 text-white p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button @click="show = false" class="inline-flex text-white hover:text-emerald-200 focus:outline-none focus:text-emerald-200 transition ease-in-out duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($clinics->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No clinic associations</h3>
                <p class="mt-1 text-sm text-gray-500">You are not currently associated with any clinics.</p>
                <div class="mt-6">
                    <p class="text-sm text-gray-500">
                        Contact your administrator to be added to a clinic.
                    </p>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($clinics as $clinic)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $clinic->id === $primaryClinic?->id ? 'bg-blue-50 border-blue-200' : 'bg-white' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($clinic->id === $primaryClinic?->id)
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    @else
                                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $clinic->name }}</h3>
                                        @if($clinic->id === $primaryClinic?->id)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <p>{{ $clinic->address }}, {{ $clinic->city }}, {{ $clinic->state }} {{ $clinic->zip }}</p>
                                        @if($clinic->phone)
                                            <p class="mt-1">{{ $clinic->phone }}</p>
                                        @endif
                                    </div>
                                    @if($clinic->description)
                                        <p class="mt-2 text-sm text-gray-600">{{ $clinic->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($clinic->id !== $primaryClinic?->id)
                                    <button wire:click="setPrimary({{ $clinic->id }})" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Set as Primary
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Primary Clinic
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Need to join or leave a clinic?
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Contact your system administrator to request changes to your clinic associations. You can only set your primary clinic from the clinics you're already associated with.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-card>
</section>