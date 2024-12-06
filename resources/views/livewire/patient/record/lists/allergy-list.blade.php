<?php

use App\Models\Allergy;
use function Livewire\Volt\{state, computed, on};

state(['patient']);

$allergies = computed(function () {
    return Allergy::where('patient_id', $this->patient->id)->get() ?? collect();
});

$delete = function ($id) {
    if (! $id) return;

    Allergy::find($id)?->delete();
    $this->dispatch('refresh');
};

on([
    'close-modal' => function ($type) {
        if ($type === 'allergy') {
            $this->dispatch('refresh');
        }
    },
]);
?>

<div>
    @if ($this->allergies->isEmpty())
        <p class="text-gray-500 text-sm">No allergies recorded</p>
    @else
        @foreach ($this->allergies as $allergy)
            <div class="text-sm flex justify-between items-start border-l-4 pl-2 {{ match ($allergy->severity) {
                'low' => 'border-green-500',
                'medium' => 'border-yellow-500',
                'high' => 'border-red-500',
                default => 'border-gray-500',
            } }}">
                <div>
                    <p class="font-medium">{{ $allergy->allergen }}</p>
                    <p class="text-gray-600">{{ $allergy->reaction }}</p>
                </div>
                <button
                    wire:click="delete({{ $allergy->id }})"
                    wire:confirm="Are you sure you want to delete this allergy record?"
                    class="text-red-500 hover:text-red-700">
                    <x-heroicon-o-trash class="w-4 h-4" />
                </button>
            </div>
            @unless ($loop->last)
                <div class="my-2 border-b border-gray-200"></div>
            @endunless
        @endforeach
    @endif
</div>
