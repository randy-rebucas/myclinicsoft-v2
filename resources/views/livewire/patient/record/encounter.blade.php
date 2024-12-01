<?php

use App\Models\Encounter;
use App\Livewire\Forms\EncounterForm;
use function Livewire\Volt\{state, form, mount, computed};

state('patient');

form(EncounterForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$encounters = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->orderBy('encounter_date', 'desc')
        ->get();
});

$create = function () {
    $this->encounterId = $this->form->store();

    $this->form->empty();

    $this->dispatch('set-encounter', encounterId: $this->encounterId);

    $this->dispatch('close-modal', 'create-new-encounter');

    $this->dispatch('refresh');
};

$delete = function (Encounter $encounter) {
    $encounter->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <x-table for="encounters">
        <x-table.thead>
            <x-table.row class="">
                <x-table.thead-cell :title="__('Chief Complaint')" class="text-left" />
                <x-table.thead-cell :title="__('Date')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click="$dispatch('open-modal', 'create-new-encounter')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="">
            @forelse ($this->encounters as $encounter)
                <x-table.row class="bg-white  cursor-pointer"
                    wire:click="filterDate({{ $encounter }})">
                    <x-table.tbody-cell :item="$encounter->chief_complaint ?? '--'" />
                    <x-table.tbody-cell :item="$encounter->encounter_date ?? '--'" class="font-bold" />
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
            @empty
                <x-table.row class="bg-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No encounter record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-encounter" :show="$errors->isNotEmpty()">
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="w-1/3">
                <x-input-label for="encounter_date" value="{{ __('Encounter Date') }}" />
                <x-text-input wire:model.live="form.encounter_date" id="encounter_date" name="encounter_date" type="date"
                    class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.encounter_date')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="chief_complaint" value="{{ __('Chief Complaint') }}" />
                <x-textarea wire:model.live="form.chief_complaint" id="chief_complaint" name="notes"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.chief_complaint')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="notes" value="{{ __('Notes') }}" />
                <x-textarea wire:model.live="form.notes" id="notes" name="notes"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
