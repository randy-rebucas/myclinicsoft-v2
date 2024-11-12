<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with(): array
    {
        return [
            'stats' => [
                ['label' => 'Total Patients', 'value' => \App\Models\Patient::count()],
                ['label' => 'New Patients', 'value' => \App\Models\Patient::whereMonth('created_at', now()->month)->count()],
                ['label' => 'Total Visits', 'value' => \App\Models\Visit::count()],
                ['label' => 'This Month', 'value' => \App\Models\Visit::whereMonth('created_at', now()->month)->count()],
            ],
        ];
    }
}; ?>

<section>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($stats as $stat)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">
                                {{ $stat['label'] }}
                            </div>
                            <div class="mt-1 text-3xl font-semibold text-gray-900">
                                {{ $stat['value'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <!-- Replace with actual activity items -->
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Welcome to your new dashboard!</p>
                                <p class="text-sm text-gray-500">Get started by customizing this template</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
