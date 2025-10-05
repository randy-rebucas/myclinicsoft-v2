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
                Welcome back, {{ $user?->name ?? 'Guest' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                @if($user && $user->roles && $user->roles->first())
                    Logged in as {{ Str::title($user->roles->first()->name) }} on {{ now()->format('l, d F Y') }}
                @else
                    Welcome on {{ now()->format('l, d F Y') }}
                @endif
            </p>
        </div>

        @if($user && $user->roles && $user->roles->first())
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

                @default
                    <livewire:dashboard.no-dashboard />
            @endswitch
        @else
            <livewire:dashboard.no-dashboard />
        @endif
    </div>
</section>
