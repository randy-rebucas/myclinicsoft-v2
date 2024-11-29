<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

?>

<section class="min-h-screen bg-gray-50/30 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-10">
            <h2 class="text-3xl font-semibold text-gray-900">
                Settings
                <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">Beta</span>
            </h2>
            <p class="mt-2 text-base text-gray-600">Configure your workspace settings and manage integrations</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <!-- Business Details Card -->
            <div class="group relative rounded-2xl bg-white p-6 ring-1 ring-gray-200 hover:ring-gray-300 transition-all duration-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            <h3 class="text-xl font-medium text-gray-900">Business Details</h3>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Update your business information and preferences
                        </p>
                    </div>
                    <div class="hidden group-hover:block">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-6">
                    <livewire:setting.form.business />
                </div>
            </div>

            <!-- Licenses Card -->
            <div class="group relative rounded-2xl bg-white p-6 ring-1 ring-gray-200 hover:ring-gray-300 transition-all duration-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                            <h3 class="text-xl font-medium text-gray-900">Licenses</h3>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Manage your license keys and subscriptions
                        </p>
                    </div>
                    <div class="hidden group-hover:block">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>
                <div class="space-y-6">
                    <livewire:setting.form.license />
                </div>
            </div>
        </div>
    </div>
</section>
