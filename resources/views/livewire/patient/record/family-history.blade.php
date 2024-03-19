<?php

use App\Models\FamilyHistory;
use App\Livewire\Forms\FamilyHistoryForm;
use function Livewire\Volt\{state, form, mount, computed};

state([
    'patient',
    'relations' => [
        'family' => 'Family',
        'friend' => 'Friend',
        'work' => 'Work',
    ],
]);

form(FamilyHistoryForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$family_histories = computed(function () {
    return FamilyHistory::where('patient_id', $this->patient->id)->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-family-history');

    $this->dispatch('refresh');
};

$delete = function (FamilyHistory $family_history) {
    $family_history->delete();

    $this->dispatch('refresh');
};

?>

<div>
    <x-table for="family-histories">
        <x-table.thead>
            <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                <x-table.thead-cell :title="__('Relationship')" class="text-left" />
                <x-table.thead-cell :title="__('Condition')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click="$dispatch('open-modal', 'create-new-family-history')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="dark:border-gray-500">
            @forelse ($this->family_histories as $family_history)
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$family_history->relationship" />
                    <x-table.tbody-cell :item="$family_history->condition" />
                    <x-table.tbody-cell :item="$family_history->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $family_history->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    </x-table.tbody-cell>
                </x-table.row>
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.thead-cell :title="__('Notes')" class="text-left" />
                    <x-table.tbody-cell :item="$family_history->notes ?? '--'" colspan="3" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No family history record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-family-history" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/2">
                    <x-input-label for="relationship" value="{{ __('Relationship') }}" />
                    <x-select wire:model="form.relationship" id="relationship" name="relationship" :options="$relations"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('form.relationship')" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <x-input-label for="condition" value="{{ __('Condition') }}" />
                    <x-text-input wire:model="form.condition" id="condition" name="condition" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.condition')" class="mt-2" />
                </div>
            </div>
            <div class="mt-4">
                <x-input-label for="notes" value="{{ __('Notes') }}" />
                <x-textarea wire:model="form.notes" id="notes" name="notes"
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
