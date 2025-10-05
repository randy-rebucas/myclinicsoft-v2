
<section x-data>
    <x-card>
        <x-slot name="header">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Professional Information') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Update your professional details and credentials.') }}
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

        @if (session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full"
                 class="fixed bottom-4 right-4 z-50 max-w-sm w-full">
                <div class="rounded-lg shadow-lg bg-red-500 text-white p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button @click="show = false" class="inline-flex text-white hover:text-red-200 focus:outline-none focus:text-red-200 transition ease-in-out duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="save">
            <div class="space-y-6">
                <!-- Specialty -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="specialty" :value="__('Specialty')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <x-text-input wire:model.live.blur="specialty" id="specialty" 
                            :placeholder="__('e.g., Internal Medicine, Cardiology')"
                            class="w-full" />
                        <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
                    </div>
                </div>

                <!-- License Number -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="license_number" :value="__('License Number')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <x-text-input wire:model.live.blur="license_number" id="license_number" 
                            :placeholder="__('Enter your medical license number')"
                            class="w-full" />
                        <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                    </div>
                </div>

                <!-- NPI Number -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="npi_number" :value="__('NPI Number')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <x-text-input wire:model.live.blur="npi_number" id="npi_number" 
                            :placeholder="__('Enter your NPI number')"
                            class="w-full" />
                        <x-input-error :messages="$errors->get('npi_number')" class="mt-2" />
                    </div>
                </div>

                <!-- Consultation Fee -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="consultation_fee" :value="__('Consultation Fee')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <x-text-input wire:model.live.blur="consultation_fee" id="consultation_fee" 
                                :placeholder="__('0.00')"
                                class="w-full pl-7" type="number" step="0.01" min="0" />
                        </div>
                        <x-input-error :messages="$errors->get('consultation_fee')" class="mt-2" />
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="phone_number" :value="__('Professional Phone')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <x-text-input wire:model.live.blur="phone_number" id="phone_number" 
                            :placeholder="__('Enter your professional phone number')"
                            class="w-full" type="tel" />
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                    </div>
                </div>

                <!-- Bio -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="bio" :value="__('Professional Bio')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <x-textarea wire:model.live.blur="bio" id="bio" 
                            :placeholder="__('Tell patients about your experience and approach to care')"
                            class="w-full" rows="4" />
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>
                </div>

                <!-- Available Hours -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="available_hours" :value="__('Available Hours')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <div class="space-y-4">
                            @foreach($available_hours as $index => $timeSlot)
                                <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                    <!-- Day Selection -->
                                    <div class="flex-shrink-0">
                                        <select wire:model.live="available_hours.{{ $index }}.day" 
                                            class="block w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                            <option value="Sunday">Sunday</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Available Toggle -->
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                            wire:model.live="available_hours.{{ $index }}.is_available"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label class="ml-2 text-sm text-gray-700">Available</label>
                                    </div>
                                    
                                    <!-- Time Inputs -->
                                    <div class="flex items-center gap-2" x-show="$wire.available_hours[{{ $index }}].is_available">
                                        <input type="time" 
                                            wire:model.live="available_hours.{{ $index }}.start_time"
                                            class="block w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <span class="text-gray-500">to</span>
                                        <input type="time" 
                                            wire:model.live="available_hours.{{ $index }}.end_time"
                                            class="block w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <button type="button" 
                                        wire:click="removeTimeSlot({{ $index }})"
                                        class="flex-shrink-0 p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                            
                            <!-- Add New Time Slot Button -->
                            <button type="button" 
                                wire:click="addTimeSlot"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Time Slot
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('available_hours')" class="mt-2" />
                    </div>
                </div>

                <!-- Meta Data -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
                    <x-input-label for="meta" :value="__('Additional Information')"
                        class="text-sm font-medium text-gray-700 md:pt-2" />
                    <div class="md:col-span-3">
                        <div class="space-y-4">
                            @if(empty($meta))
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">No additional information added yet.</p>
                                    <p class="text-xs text-gray-400">Add custom fields for any additional professional details.</p>
                                </div>
                            @else
                                @foreach($meta as $index => $metaItem)
                                    <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                        <!-- Key Input -->
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Key</label>
                                            <input type="text" 
                                                wire:model.live="meta.{{ $index }}.key"
                                                placeholder="e.g., certifications, languages, etc."
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <!-- Value Input -->
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Value</label>
                                            <input type="text" 
                                                wire:model.live="meta.{{ $index }}.value"
                                                placeholder="Enter the value"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        
                                        <!-- Remove Button -->
                                        <button type="button" 
                                            wire:click="removeMetaField({{ $index }})"
                                            class="flex-shrink-0 p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                            
                            <!-- Add New Meta Field Button -->
                            <button type="button" 
                                wire:click="addMetaField"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Custom Field
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('meta')" class="mt-2" />
                    </div>
                </div>
</div>

            <div class="mt-6 flex justify-end">
                <x-primary-button wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('Save Changes') }}</span>
                    <span wire:loading wire:target="save" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ __('Saving...') }}
                    </span>
                </x-primary-button>
            </div>
        </form>
    </x-card>
</section>