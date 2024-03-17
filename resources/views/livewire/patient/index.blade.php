<?php

use App\Models\Patient;
use function Livewire\Volt\{layout, protect, state, on, with, usesPagination};

layout('layouts.app');

state(['search']);

usesPagination();

on(['patient-created']);

with(fn() => ['patients' => Patient::where('first_name', 'like', '%' . $this->search . '%')->paginate(10)]);

$delete = function (Patient $patient) {
    $patient->delete();
};

$form = function ($type, ?Patient $patient) {
    $this->redirectRoute('patient-form', ['state' => $type, 'patient' => $patient]);
};

$detail = function (Patient $patient) {
    // $this->ensureDetailCanBeViewed();

    $this->redirectRoute('patient-detail', ['patientId' => $patient]);
};

// $ensureDetailCanBeViewed = protect(function () {
//     return;
// });

?>
<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patients') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="min-w-full">
                    <div class="space-y-6">
                        <div class="flex justify-between">
                            <x-text-input wire:model.live="search" class="py-2" type="search" :placeholder="__('Search Patient...')" />
                            <x-secondary-button wire:click="form('create')">
                                {{ __('Create New') }}
                            </x-secondary-button>
                        </div>

                        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
                            <x-table for="patient">
                                <x-table.thead>
                                    <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                                        <x-table.thead-cell :title="__('Full Name')" class="text-left" />
                                        <x-table.thead-cell :title="__('Phone Number')" class="text-center" />
                                        <x-table.thead-cell :title="__('Gender')" class="text-center" />
                                        <x-table.thead-cell :title="__('Birthdate')" class="text-center" />
                                        <x-table.thead-cell :title="__('Age')" class="text-center" />
                                        <x-table.thead-cell title="" class="text-right" />
                                    </x-table.row>
                                </x-table.thead>
                                <x-table.tbody class="dark:border-gray-500">
                                    @forelse ($patients as $patient)
                                        <x-table.row class="bg-white dark:bg-gray-700 dark:text-white"
                                            wire:loading.class="opacity-50">
                                            <x-table.tbody-cell :item="$patient->full_name" />
                                            <x-table.tbody-cell :item="$patient->phone_number" class="text-center" />
                                            <x-table.tbody-cell :item="$patient->gender" class="text-center uppercase" />
                                            <x-table.tbody-cell :item="$patient->date_of_birth" class="text-center" />
                                            <x-table.tbody-cell :item="$patient->age" class="text-center uppercase" />
                                            <x-table.tbody-cell :item="$patient->id" class="text-right"
                                                :action="true">
                                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                                    wire:click="detail({{ $patient }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                                                    </svg>

                                                </button>
                                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                                    wire:click="form('edit', {{ $patient }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path
                                                            d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-info m-1 text-red-600 font-medium underline"
                                                    wire:click="delete({{ $patient }})"
                                                    wire:confirm="Are you sure you want to delete this patient?">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd"
                                                            d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </x-table.tbody-cell>
                                        </x-table.row>
                                    @empty
                                        <x-table.row class="bg-white dark:bg-gray-700 dark:text-white">
                                            <x-table.tbody-cell colspan="6" :item="__('No patient found!!')" />
                                        </x-table.row>
                                    @endforelse
                                </x-table.tbody>
                            </x-table>
                        </div>
                        <div>
                            {{ $patients->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
