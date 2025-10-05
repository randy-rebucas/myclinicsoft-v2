<?php

use App\Models\MedicalCondition;
use function Livewire\Volt\{state, computed, on};

state(['patient', 'open' => false]);
// Event Handlers
on([
    'medical-conditions-refreshed' => function () {
        $this->dispatch('refresh');
    },
]);

$medicalConditions = computed(fn() => MedicalCondition::where('patient_id', $this->patient->id)->get() ?? collect());

$delete = function ($id) {
    MedicalCondition::find($id)->delete();
    $this->dispatch('refresh');
};

$showModal = function ($type, $title, $form) {
    $this->dispatch('show-modal', ['type' => $type, 'title' => $title, 'form' => $form]);
};
?>

<div x-data="{ open: @entangle('open') }" class="border rounded-md">
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
        <span>Medical Conditions ({{ $this->medicalConditions->count() }})</span>
        <span class="transform transition-transform duration-200" :class="{ 'rotate-180': open }">
            <x-heroicon-o-chevron-down class="w-4 h-4" />
        </span>
    </button>
    <div x-show="open" class="px-4 py-2 bg-gray-50">
        <div class="space-y-2">
            @foreach ($this->medicalConditions as $condition)
                <div class="text-sm flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $condition->condition_name }}</p>
                        <p class="text-gray-600">
                            {{ $condition->diagnosis_date ? $condition->diagnosis_date->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                    <button wire:click="delete({{ $condition->id }})"
                        wire:confirm="Are you sure you want to delete this medical condition record?"
                        class="text-red-500 hover:text-red-700">
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                </div>
                @unless ($loop->last)
                    <div class="my-2 border-b border-gray-200"></div>
                @endunless
            @endforeach

            <button wire:click="showModal('add', 'Add New Medical Condition', 'medical-condition')"
                class="w-full text-left text-sm text-blue-600 hover:text-blue-700">
                + Add Medical Condition
            </button>
        </div>
    </div>
</div>
