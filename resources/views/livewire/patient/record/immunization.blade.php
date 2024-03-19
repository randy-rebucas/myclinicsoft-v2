<?php

use App\Models\Immunization;
use App\Livewire\Forms\ImmunizationForm;
use function Livewire\Volt\{state, form, mount, computed};

state([
    'patient',
    'administrators' => [
        'physician' => 'Physician',
        'nurse' => 'Nurse',
    ],
]);

form(ImmunizationForm::class);

mount(function () {
    $this->form->patient_id = $this->patient->id;
});

$immunizations = computed(function () {
    return Immunization::where('patient_id', $this->patient->id)->get();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-immunization');

    $this->dispatch('refresh');
};

$delete = function (Immunization $immunization) {
    $immunization->delete();

    $this->dispatch('refresh');
};
?>

<div>
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Immunization') }}</h3>
    <x-table for="family-histories">
        <x-table.thead>
            <x-table.row class="dark:bg-gray-900 dark:text-gray-100">
                <x-table.thead-cell :title="__('Vaccine Name')" class="text-left" />
                <x-table.thead-cell :title="__('Date Administered')" class="text-left" />
                <x-table.thead-cell :title="__('Administrator')" class="text-left" />
                <x-table.thead-cell title="" :action="true" class="text-right">
                    <button type="button" class="btn btn-info m-1 font-medium underline" x-data=""
                        x-on:click="$dispatch('open-modal', 'create-new-immunization')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path
                                d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>
                </x-table.thead-cell>
            </x-table.row>
        </x-table.thead>
        <x-table.tbody class="dark:border-gray-500">
            @forelse ($this->immunizations as $immunization)
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white" wire:loading.class="opacity-50">
                    <x-table.tbody-cell :item="$immunization->vaccine_name" />
                    <x-table.tbody-cell :item="$immunization->date_administered" />
                    <x-table.tbody-cell :item="$immunization->administrator" class="uppercase"/>
                    <x-table.tbody-cell :item="$immunization->id" class="text-right md:py-1" :action="true">
                        <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                            wire:click="delete('{{ $immunization->id }}')">
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
                    <x-table.tbody-cell :item="$immunization->notes ?? '--'" colspan="4" />
                </x-table.row>
            @empty
                <x-table.row class="bg-white dark:bg-gray-700 dark:text-white text-center">
                    <x-table.tbody-cell colspan="7" :item="__('No immunization record')" />
                </x-table.row>
            @endforelse
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-immunization" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/3">
                    <x-input-label for="vaccine_name" value="{{ __('Vaccine Name') }}" />
                    <x-text-input wire:model="form.vaccine_name" id="vaccine_name" name="vaccine_name" type="text"
                        class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.vaccine_name')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="administrator" value="{{ __('Administrator') }}" />
                    <x-select wire:model="form.administrator" id="administrator" name="administrator" :options="$administrators"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('form.administrator')" class="mt-2" />
                </div>
                <div class="w-1/3">
                    <x-input-label for="date_administered" value="{{ __('Date Administered') }}" />
                    <x-text-input wire:model="form.date_administered" id="date_administered" name="date_administered"
                        type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.date_administered')" class="mt-2" />
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
    @push('scripts')
        <script>
            var picker = new Pikaday({
                field: document.getElementById('date_administered'),
                format: 'D/M/YYYY',
                toString(date, format) {
                    // you should do formatting based on the passed format,
                    // but we will just return 'D/M/YYYY' for simplicity
                    const day = date.getDate();
                    const month = date.getMonth() + 1;
                    const year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                },
                onSelect: function() {
                    @this.set('form.date_administered', picker.toString());
                }
            });
        </script>
    @endpush
</div>
