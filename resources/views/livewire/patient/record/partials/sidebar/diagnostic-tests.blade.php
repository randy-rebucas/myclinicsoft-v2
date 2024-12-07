<?php

use App\Models\DiagnosticTest;
use function Livewire\Volt\{state, computed, on};

state(['patient', 'open' => false]);

// Event Handlers
on([
    'diagnostic-tests-refreshed' => function () {
        $this->dispatch('refresh');
    },
]);

$diagnosticTests = computed(fn() => DiagnosticTest::where('patient_id', $this->patient->id)->get() ?? collect());

$delete = function ($id) {
    DiagnosticTest::find($id)->delete();
    $this->dispatch('refresh');
};

$showModal = function ($type, $title, $form) {
    $this->dispatch('show-modal', ['type' => $type, 'title' => $title, 'form' => $form]);
};
?>

<div x-data="{ open: @entangle('open') }" class="border rounded-md">
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
        <span>Diagnostic Tests ({{ $this->diagnosticTests->count() }})</span>
        <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
            <x-heroicon-o-chevron-down class="w-4 h-4" />
        </span>
    </button>
    <div x-show="open" class="px-4 py-2 bg-gray-50">
        <div class="space-y-2">
            @foreach ($this->diagnosticTests as $test)
                <div class="text-sm flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $test->test_name }}</p>
                        <p class="text-gray-600">
                            {{ $test->date ? $test->date->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <button wire:click="delete({{ $test->id }})"
                        wire:confirm="Are you sure you want to delete this diagnostic test record?"
                        class="text-red-500 hover:text-red-700">
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                </div>
                @unless ($loop->last)
                    <div class="my-2 border-b border-gray-200"></div>
                @endunless
            @endforeach

            <button wire:click="showModal('add', 'Add New Diagnostic Test', 'diagnostic-test')"
                class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                + Add Diagnostic Test
            </button>
        </div>
    </div>
</div>
