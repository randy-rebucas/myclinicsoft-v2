<?php

use App\Models\Encounter;
use App\Livewire\Forms\EncounterForm;
use function Livewire\Volt\{state, form, mount, computed};

state('patient');

form(EncounterForm::class);

mount(function () {
    $this->form->encounter_date = date('Y-m-d');
    $this->form->patient_id = $this->patient->id;
});

$encounter = computed(function () {
    return Encounter::where('patient_id', $this->patient->id)
        ->get()
        ->first();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-encounter');

    $this->dispatch('refresh');
};
?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md" wire:loading.class="opacity-50">
        <legend class="dark:text-gray-200 px-2">{{ __('Latest Encounter') }}</legend>

        @if ($this->encounter)
            <livewire:patient.encounter.physical-examination :encounter="$this->encounter" />
            <livewire:patient.encounter.diagnostic-test :encounter="$this->encounter" />
        @else
            <div class="flex flex-col items-center">
                <p class="mb-3">{{ __('no encounter recorded yet.') }}</p>
                <x-secondary-button class="ms-3 py-3" x-data=""
                    x-on:click="$dispatch('open-modal', 'create-new-encounter')">
                    {{ __('Create Encounter') }}
                </x-secondary-button>
            </div>
        @endif
    </fieldset>
    <x-modal name="create-new-encounter" :show="$errors->isNotEmpty()">
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="w-1/3">
                <x-input-label for="encounter_date" value="{{ __('Encounter Date') }}" />
                <x-text-input wire:model="form.encounter_date" id="encounter_date" name="encounter_date" type="text"
                    class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.encounter_date')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="chief_complaint" value="{{ __('Chief Complaint') }}" />
                <x-textarea wire:model="form.chief_complaint" id="chief_complaint" name="notes"
                    class="block mt-1 w-full"></x-textarea>
                <x-input-error :messages="$errors->get('form.chief_complaint')" class="mt-2" />
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
                field: document.getElementById('encounter_date'),
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
                    @this.set('form.encounter_date', picker.toString());
                }
            });
        </script>
    @endpush
</div>
