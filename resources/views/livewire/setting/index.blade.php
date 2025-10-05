<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state(['activeTab' => 'profile']);

?>

<section class="min-h-screen bg-gray-50/30 py-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex gap-6">
            <!-- Side Tab Navigation -->
            <div class="w-64 shrink-0">
                <nav class="flex flex-col space-y-1" aria-label="Tabs">
                    <button wire:click="$set('activeTab', 'profile')"
                        class="{{ $activeTab === 'profile' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} flex items-center gap-3 px-3 py-2 text-sm font-medium border-l-4">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12zm0 1.5c-2.485 0-7.5 1.243-7.5 3.75V21h15v-3.75c0-2.507-5.015-3.75-7.5-3.75z" />
                        </svg>
                        Profile
                    </button>
                    @hasanyrole('doctor')
                        <button wire:click="$set('activeTab', 'professional')"
                            class="{{ $activeTab === 'professional' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} flex items-center gap-3 px-3 py-2 text-sm font-medium border-l-4">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0z" />
                            </svg>
                            Professional Info
                        </button>
                    @endhasanyrole
                    @hasanyrole('doctor|admin')
                        <button wire:click="$set('activeTab', 'clinics')"
                            class="{{ $activeTab === 'clinics' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} flex items-center gap-3 px-3 py-2 text-sm font-medium border-l-4">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            Clinic Associations
                        </button>
                    @endhasanyrole
                </nav>
            </div>

            <!-- Tab Panels -->
            <div class="flex-1">
                <div x-show="$wire.activeTab === 'profile'">
                    <x-card>
                        <livewire:user.profile.update-profile-information-form />
                    </x-card>

                    <x-card class="mt-6">
                        <livewire:user.profile.update-password-form />
                    </x-card>

                    <x-card class="mt-6">
                        <livewire:user.profile.delete-user-form />
                    </x-card>
                </div>
                @hasanyrole('doctor')
                    <div x-show="$wire.activeTab === 'professional'">
                        <x-card>
                            <livewire:setting.form.professional />
                        </x-card>
                    </div>
                @endhasanyrole
                @hasanyrole('doctor|admin')
                    <div x-show="$wire.activeTab === 'clinics'">
                        <x-card>
                            <livewire:setting.form.clinic-associations />
                        </x-card>
                    </div>
                @endhasanyrole
            </div>
        </div>
    </div>
</section>
