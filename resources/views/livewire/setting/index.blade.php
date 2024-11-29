<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state(['activeTab' => 'business']);

?>

<section class="min-h-screen bg-gray-50/30 py-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex gap-6">
            <!-- Side Tab Navigation -->
            <div class="w-64 shrink-0">
                <nav class="flex flex-col space-y-1" aria-label="Tabs">
                    <button
                        wire:click="$set('activeTab', 'business')"
                        class="{{ $activeTab === 'business' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} flex items-center gap-3 px-3 py-2 text-sm font-medium border-l-4"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        Business Details
                    </button>
                    <button
                        wire:click="$set('activeTab', 'licenses')"
                        class="{{ $activeTab === 'licenses' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} flex items-center gap-3 px-3 py-2 text-sm font-medium border-l-4"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        Licenses
                    </button>
                </nav>
            </div>

            <!-- Tab Panels -->
            <div class="flex-1">
                <div x-show="$wire.activeTab === 'business'">
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-200">
                        <livewire:setting.form.business />
                    </div>
                </div>

                <div x-show="$wire.activeTab === 'licenses'">
                    <div class="rounded-2xl bg-white p-6 ring-1 ring-gray-200">
                        <livewire:setting.form.license />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
