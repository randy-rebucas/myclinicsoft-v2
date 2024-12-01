<?php

use App\Models\Encounter;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Livewire\Forms\QueueForm;
use function Livewire\Volt\{state, form, on, mount, computed};

state(['patient', 'encounterId', 'filter', 'show' => false]);

form(QueueForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$queue = computed(function () {
    return Queue::where('patient_id', $this->patient->id)
        ->where('status', 'waiting')
        ->latest()
        ->first();
});

$create = function () {
    $this->form->store();

    $this->form->empty();
};

$filterDate = function (Encounter $encounter) {
    $this->filter = $encounter->encounter_date;
    $this->show = false;
    $this->dispatch('set-encounter', encounterId: $encounter->id);
};

$encounters = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->when($this->filter, function ($query) {
            return $query->whereDate('encounter_date', $this->filter);
        })
        ->latest('encounter_date')
        ->get();
});

on([
    'encounter' => function () {
        $this->dispatch('refresh');
    },
]);

?>

<div>
    <x-table for="encounters">
        <x-table.thead>
            <x-table.row class="">
                <x-table.thead-cell :title="__('Chief Complaint')" class="text-left" />
                <x-table.thead-cell :title="__('Encounetr Date')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    {{-- <button type="button" class="btn btn-info m-1 font-medium underline"
                                x-data="" x-on:click="$dispatch('open-modal', 'create-new-immunization')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-5 h-5">
                                    <path
                                        d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                                </svg>
                            </button> --}}
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="">
            @forelse ($this->encounters as $encounter)
                <x-table.row class="bg-white " wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$encounter->chief_complaint" />
                    <x-table.tbody-cell :item="$encounter->encounter_date" />
                    <x-table.tbody-cell :item="$encounter->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $encounter->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </x-table.tbody-cell>
                </x-table.row>
                <x-table.row class="bg-white " wire:loading.class="opacity-50">
                    <x-table.thead-cell :title="__('Notes')" class="text-left" />
                    <x-table.tbody-cell :item="$encounter->notes ?? '--'" colspan="4" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white  text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No encounter record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
</div>
