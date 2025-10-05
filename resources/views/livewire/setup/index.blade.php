<?php

use App\Livewire\Forms\SetupForm;
use function Livewire\Volt\{state, layout, form};

form(SetupForm::class);

layout('layouts.guest');

$save = function () {
    $this->form->store();
};

?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Setup') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Title and Descriptions -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 rounded-full mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-3">System Setup</h1>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        Welcome to your clinic management system. This setup will create your admin account and configure essential system settings.
                    </p>
                </div>
            </div>

            <!-- Complete Setup Process -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Setup Process</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">1</div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Create Superadmin Account</h3>
                            <p class="text-sm text-gray-600">Set up the first admin user with full system access</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold">2</div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Configure Permissions</h3>
                            <p class="text-sm text-gray-600">Set up user roles and permission system</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold">3</div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Setup Clinic Settings</h3>
                            <p class="text-sm text-gray-600">Configure default clinic information and settings</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-semibold">4</div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">System Ready</h3>
                            <p class="text-sm text-gray-600">Complete setup and ready to use</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Info -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">What Will Be Configured</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">User Roles & Permissions</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Admin role with full system access</li>
                            <li>• Doctor role with medical permissions</li>
                            <li>• Patient role with basic access</li>
                            <li>• Comprehensive permission system</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Clinic Settings</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Default clinic information</li>
                            <li>• Contact details and hours</li>
                            <li>• System preferences</li>
                            <li>• Customizable settings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Admin Account</h2>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input 
                            wire:model.live="form.name" 
                            id="name" 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Enter your full name"
                            required 
                            autofocus 
                        />
                        @error('form.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input 
                            wire:model.live="form.email" 
                            id="email" 
                            type="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Enter your email address"
                            required 
                        />
                        @error('form.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input 
                            wire:model.live="form.password" 
                            id="password" 
                            type="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Create a strong password"
                            required 
                        />
                        @error('form.password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input 
                            wire:model.live="form.password_confirmation" 
                            id="password_confirmation" 
                            type="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Confirm your password"
                            required 
                        />
                        @error('form.password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit" 
                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Complete Setup
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">After Setup</h3>
                    <p class="text-sm text-gray-600">
                        You'll be automatically logged in and can start using all system features. 
                        Additional users and settings can be configured from the admin panel.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>