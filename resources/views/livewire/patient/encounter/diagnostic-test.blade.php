<?php

use Illuminate\Support\Facades\DB;
use App\Models\DiagnosticTest;
use function Livewire\Volt\{state};

state([
    'diagnostic_test' => fn($encounter) => DiagnosticTest::where('encounter_id', $encounter->id)
        ->orderBy('test_date', 'desc')
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
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Diagnostic Test') }}</h3>
    <x-table for="diagnostic_test">
        <x-table.tbody class="dark:border-gray-500">
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Test Name')" class="text-left" />
                <x-table.tbody-cell :item="$diagnostic_test->test_name ?? '--'" />

                <x-table.thead-cell :title="__('Date')" class="text-left" />
                <x-table.tbody-cell :item="$diagnostic_test->test_date  ?? '--'" class="font-bold"/>
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Results')" class="text-left" />
                <x-table.tbody-cell :item="$diagnostic_test->results  ?? '--'" colspan="3" />
            </x-table.row>
            <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                <x-table.thead-cell :title="__('Notes')" class="text-left" />
                <x-table.tbody-cell :item="$diagnostic_test->notes  ?? '--'" colspan="3" />
            </x-table.row>
        </x-table.tbody>
    </x-table>
</div>
