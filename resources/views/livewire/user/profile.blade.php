<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $activeTab = 'profile';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
}; ?>

<section class="min-h-screen bg-gray-50/30 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-10">
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('Profile') }}
            </h2>
            <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                {{ __('Manage your account settings and preferences') }}
            </p>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
                <!-- Side Navigation -->
                <aside class="py-6 px-2 sm:px-6 lg:col-span-3">
                    <nav class="space-y-1">
                        <button wire:click="setTab('profile')"
                            class="w-full text-left {{ $activeTab === 'profile'
                                ? 'bg-gray-50 dark:bg-gray-800/60 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-900 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/60' }}
                                    group rounded-md px-3 py-2 flex items-center text-sm font-medium">
                            <svg class="{{ $activeTab === 'profile'
                                ? 'text-indigo-500 dark:text-indigo-400'
                                : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}
                                -ml-1 mr-3 h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('Profile') }}
                        </button>

                        <button wire:click="setTab('security')"
                            class="w-full text-left {{ $activeTab === 'security'
                                ? 'bg-gray-50 dark:bg-gray-800/60 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-900 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/60' }}
                                    group rounded-md px-3 py-2 flex items-center text-sm font-medium">
                            <svg class="{{ $activeTab === 'security'
                                ? 'text-indigo-500 dark:text-indigo-400'
                                : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}
                                -ml-1 mr-3 h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            {{ __('Security') }}
                        </button>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="space-y-6 sm:px-6 lg:col-span-9">
                    @if ($activeTab === 'profile')
                        <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl shadow-sm rounded-lg">
                            <div class="p-6 sm:p-8">
                                <div class="flex flex-col gap-2">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ __('Personal Information') }}
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Update your account\'s profile information.') }}
                                    </p>
                                </div>

                                <div class="mt-6">
                                    <livewire:user.profile.update-profile-information-form />
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($activeTab === 'security')
                        <div class="bg-white dark:bg-gray-800/50 backdrop-blur-xl shadow-sm rounded-lg">
                            <div class="p-6 sm:p-8">
                                <div class="flex flex-col gap-2">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ __('Security') }}
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Ensure your account is using a secure password.') }}
                                    </p>
                                </div>

                                <div class="mt-6">
                                    <livewire:user.profile.update-password-form />
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
