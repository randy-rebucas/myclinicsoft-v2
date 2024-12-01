<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

// Add state for user and role
state(['user' => auth()->user()]);

?>

<section class="min-h-screen">
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Welcome Banner -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                Welcome back, {{ $user->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Logged in as {{ Str::title($user->roles->first()->name) }} on {{ now()->format('l, d F Y') }}
            </p>
        </div>

        @switch($user->roles->first()->name)
            @case('doctor')
                <livewire:dashboard.doctor />
            @break

            @case('receptionist')
                <livewire:dashboard.receptionist />
            @break

            @case('patient')
                <livewire:dashboard.patient />
            @break

            @case('admin')
                <livewire:dashboard.admin />
            @break

            @case('medrep')
                <livewire:dashboard.med-representative />
            @break

            @default
                <livewire:dashboard.no-dashboard />
        @endswitch
    </div>
</section>
