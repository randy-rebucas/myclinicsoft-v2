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

<div>
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
