<?php

use App\Models\Allergy;
use App\Livewire\Forms\AllergyForm;
use function Livewire\Volt\{on, state, form, mount};

on(['refresh']);
state([
    'allergies' => fn($patient) => Allergy::where('patient_id', $patient->id)->get(),
]);

form(AllergyForm::class);

mount(function ($patientId) {
    $this->form->setPatientId($patientId);
});

$create = function () {
    $this->form->store();

    $this->dispatch('refresh');
    $this->dispatch('close');
    $this->dispatch('close-modal');
};

$delete = function (Allergy $allergy) {
    $allergy->delete();

    $this->dispatch('refresh');
};
?>

<div>
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Allergies') }}</h3>
    <x-table for="allergies">
        <x-table.thead>
            <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                <x-table.thead-cell :title="__('Allergen')" class="text-left" />
                <x-table.thead-cell :title="__('Reaction')" class="text-left" />
                <x-table.thead-cell :title="__('Severity')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-new-allergy')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="dark:border-gray-500">
            @forelse ($allergies as $allergy)
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$allergy->allergen" />
                    <x-table.tbody-cell :item="$allergy->reaction" />
                    <x-table.tbody-cell :item="$allergy->severity" />
                    <x-table.tbody-cell :item="$allergy->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $allergy->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </x-table.tbody-cell>
                </x-table.row>
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.thead-cell :title="__('Notes')" class="text-left" />
                    <x-table.tbody-cell :item="$allergy->notes" colspan="4" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No allergy record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-allergy" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/3">
                    <x-input-label for="allergen" value="{{ __('Allergen') }}" />
                    <x-text-input wire:model="form.allergen" id="allergen" name="allergen" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.allergen')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="reaction" value="{{ __('Reaction') }}" />
                    <x-text-input wire:model="form.reaction" id="reaction" name="reaction" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.reaction')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="severity" value="{{ __('Severity') }}" />
                    <x-text-input wire:model="form.severity" id="severity" name="severity" type="text"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('form.severity')" class="mt-2" />
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
