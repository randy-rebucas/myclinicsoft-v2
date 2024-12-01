<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="space-y-6">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button
            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-blue-500 transition-colors">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="ml-3 font-medium text-gray-900">New Appointment</span>
        </button>
        <!-- Add more quick action buttons -->
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Today's Schedule</h2>
            </div>
            <div class="p-6">
                <!-- Add schedule component here -->
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Recent Registrations</h2>
            </div>
            <div class="p-6">
                <!-- Add registrations component here -->
            </div>
        </div>
    </div>
</div>
