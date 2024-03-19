<?php

use App\Models\PhysicalExamination;
use function Livewire\Volt\{state};

state([
    'physical_exam' => fn($encounter) => PhysicalExamination::where('encounter_id', $encounter->id)
        ->orderBy('created_at', 'desc')
        ->latest()
        ->first(),
]);
?>

<div class="relative">
    <button type="button" class="btn btn-info m-1 font-medium underline absolute right-0 top-2" x-data=""
        x-on:click="$dispatch('open-modal', 'create-new-allergy')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
            <path
                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
    </button>
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Physical Exam') }}</h3>
    <x-table for="physical_exam">
        <x-table.tbody class="dark:border-gray-500">
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('General Apperance')" class="text-left" />
                <x-table.tbody-cell :item="$physical_exam->general_appearance ?? '--'" />
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Systematic Findings')" class="text-left" />
                <x-table.tbody-cell :item="$physical_exam->systematic_findings ?? '--'" />
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Notes')" class="text-left" />
                <x-table.tbody-cell :item="$physical_exam->notes ?? '--'" />
            </x-table.row>
        </x-table.tbody>
    </x-table>
</div>
