<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function emailUpdated()
    {
        $this->dispatch('refresh');
    }
}; ?>

<nav x-data="{ open: false }" class="">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                <!-- Navigation Links -->
                @hasanyrole('doctor')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </x-nav-link>
                        @can('view patients')
                            <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ __('Patients') }}</span>
                            </x-nav-link>
                        @endcan
                        @can('view queue')
                            <x-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                <span>{{ __('Queue') }}</span>
                            </x-nav-link>
                        @endcan
                        @can('manage appointments')
                            <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('Appointments') }}</span>
                            </x-nav-link>
                        @endcan
                        @can('view encounters')
                            <x-nav-link :href="route('encounters.index')" :active="request()->routeIs('encounters.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" />
                                </svg>
                                <span>{{ __('Encounters') }}</span>
                            </x-nav-link>
                        @endcan
                        @can('view prescriptions')
                            <x-nav-link :href="route('prescriptions.index')" :active="request()->routeIs('prescriptions.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>{{ __('Prescriptions') }}</span>
                            </x-nav-link>
                        @endcan
                    </div>
                @endhasanyrole
                
                <!-- Patient Navigation -->
                @hasanyrole('patient')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </x-nav-link>
                        @can('view patient records')
                            <x-nav-link :href="route('patients.show', auth()->user()->patient->id)" :active="request()->routeIs('patients.show')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ __('My Records') }}</span>
                            </x-nav-link>
                        @endcan
                    </div>
                @endhasanyrole
                
                <!-- Medical Representative Navigation -->
                @hasanyrole('medrep')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </x-nav-link>
                        @can('view doctors')
                            <x-nav-link :href="route('doctors.index')" :active="request()->routeIs('doctors.*')" wire:navigate>
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ __('Doctors') }}</span>
                            </x-nav-link>
                        @endcan
                    </div>
                @endhasanyrole
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm pe-1">
                            <div class="flex items-center hover:text-gray-700">
                                @if (auth()->user()->avatar)
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatar }}"
                                        alt="{{ auth()->user()->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium text-sm">
                                            {{ substr(auth()->user()->name, 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-3 me-2 text-left">
                                <div class="text-gray-900" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                    x-on:profile-updated.window="name = $event.detail.name"></div>
                                <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-gray-200">
                            <div class="px-4 py-3">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <div class="py-1">
                            @hasanyrole('doctor|admin')
                                <x-dropdown-link :href="route('settings.index')" wire:navigate>
                                    {{ __('Settings') }}
                                </x-dropdown-link>
                            @endhasanyrole
                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        @hasanyrole('doctor')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    <span>{{ __('Dashboard') }}</span>
                </x-responsive-nav-link>
                @can('view patients')
                    <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" wire:navigate>
                        <span>{{ __('Patients') }}</span>
                    </x-responsive-nav-link>
                @endcan
                @can('view queue')
                    <x-responsive-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.*')" wire:navigate>
                        <span>{{ __('Queue') }}</span>
                    </x-responsive-nav-link>
                @endcan
                @can('manage appointments')
                    <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')" wire:navigate>
                        <span>{{ __('Appointments') }}</span>
                    </x-responsive-nav-link>
                @endcan
                @can('view encounters')
                    <x-responsive-nav-link :href="route('encounters.index')" :active="request()->routeIs('encounters.*')" wire:navigate>
                        <span>{{ __('Encounters') }}</span>
                    </x-responsive-nav-link>
                @endcan
                @can('view prescriptions')
                    <x-responsive-nav-link :href="route('prescriptions.index')" :active="request()->routeIs('prescriptions.*')" wire:navigate>
                        <span>{{ __('Prescriptions') }}</span>
                    </x-responsive-nav-link>
                @endcan
            </div>
        @endhasanyrole
        
        <!-- Patient Responsive Navigation -->
        @hasanyrole('patient')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    <span>{{ __('Dashboard') }}</span>
                </x-responsive-nav-link>
                @can('view patient records')
                    <x-responsive-nav-link :href="route('patients.show', auth()->user()->patient->id)" :active="request()->routeIs('patients.show')" wire:navigate>
                        <span>{{ __('My Records') }}</span>
                    </x-responsive-nav-link>
                @endcan
            </div>
        @endhasanyrole
        
        <!-- Medical Representative Responsive Navigation -->
        @hasanyrole('medrep')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    <span>{{ __('Dashboard') }}</span>
                </x-responsive-nav-link>
                @can('view doctors')
                    <x-responsive-nav-link :href="route('doctors.index')" :active="request()->routeIs('doctors.*')" wire:navigate>
                        <span>{{ __('Doctors') }}</span>
                    </x-responsive-nav-link>
                @endcan
            </div>
        @endhasanyrole

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center">
                @if (auth()->user()->avatar)
                    <img class="h-10 w-10 rounded-full object-cover me-3" src="{{ auth()->user()->avatar }}"
                        alt="{{ auth()->user()->name }}">
                @else
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center me-3">
                        <span class="text-gray-600 font-medium">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </span>
                    </div>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                        x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @hasanyrole('doctor|admin')
                    <x-responsive-nav-link :href="route('settings.index')" wire:navigate>
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                @endhasanyrole
                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
