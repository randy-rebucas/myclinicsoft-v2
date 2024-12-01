<?php

use App\Models\PhysicalExamination;
use App\Livewire\Forms\PhysicalExaminationForm;
use function Livewire\Volt\{state, form, rules, mount, computed};

state([
    'patientId',
    'items',
    'types' => [
        'weight' => 'Weight',
        'height' => 'Height',
        'temperature' => 'Temperature',
    ],
]);

form(PhysicalExaminationForm::class);

mount(function () {
    $this->form->patient_id = $this->patientId;
    $this->items[] = [];
});

$physical_exam = computed(function () {
    return PhysicalExamination::where('patient_id', $this->patientId)
        ->orderBy('created_at', 'desc')
        ->latest()
        ->first();
});

$create = function () {
    $this->form->store();

    $this->form->empty();

    $this->dispatch('close-modal', 'create-new-physical-exam');

    $this->dispatch('refresh');
};

$add = function () {
    $item = new stdClass();
    $item->type = '';
    $item->value = '';
    array_push($this->items, $item);
};

$remove = function ($index) {
    array_splice($this->items, $index, 1);
};
?>

<div class="relative">
    @if (!$this->physical_exam)
        <button type="button" class="btn btn-info m-1 font-medium underline absolute right-0 top-2"
            x-data="" x-on:click="$dispatch('open-modal', 'create-new-physical-exam')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path
                    d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
        </button>
    @endif
    <h3 class="text-xl font-bold text-navy-700 ">{{ __('Physical Exam') }}</h3>
    <x-table for="physical_exam">
        <x-table.tbody class="">
            <x-table.row class="bg-white ">
                <x-table.thead-cell :title="__('General Apperance')" class="text-left" />
                <x-table.tbody-cell :item="$this->physical_exam->general_appearance ?? '--'" colspan="2" />
                <x-table.thead-cell :title="__('Systematic Findings')" class="text-left" />
                <x-table.tbody-cell :item="$this->physical_exam->systematic_findings ?? '--'" colspan="2" />
            </x-table.row>
            <x-table.row class="bg-white ">
                @if ($this->physical_exam)
                    @foreach ($this->physical_exam->vital_signs as $vital_sign)
                        <x-table.thead-cell :title="__($vital_sign['type'])" class="text-left" />
                        <x-table.tbody-cell :item="$vital_sign['value']" :action="true">
                            @php
                                switch ($vital_sign['type']) {
                                    case 'weight':
                                        $unit = '(kg)';
                                        break;
                                    case 'height':
                                        $unit = '(cm)';
                                        break;
                                    case 'temperature':
                                        $unit = '(°F)';
                                        break;
                                    default:
                                        $unit = '';
                                        break;
                                }
                            @endphp
                            {{ $vital_sign['value'] . ' ' . $unit }}
                        </x-table.tbody-cell>
                    @endforeach
                @endif
            </x-table.row>
            <x-table.row class="bg-white ">
                <x-table.thead-cell :title="__('Notes')" class="text-left" />
                <x-table.tbody-cell :item="$this->physical_exam->notes ?? '--'" colspan="5" />
            </x-table.row>
        </x-table.tbody>
    </x-table>
    <x-modal name="create-new-physical-exam" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="create" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New') }}
            </h2>
            <div class="flex justify-between gap-4">
                <div class="w-1/2">
                    <x-input-label for="general_appearance" value="{{ __('General Apperance') }}" />
                    <x-text-input wire:model.live="form.general_appearance" id="general_appearance" name="general_appearance"
                        type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.general_appearance')" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <x-input-label for="systematic_findings" value="{{ __('Systematic Findings') }}" />
                    <x-text-input wire:model.live="form.systematic_findings" id="systematic_findings"
                        name="systematic_findings" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.systematic_findings')" class="mt-2" />
                </div>
            </div>
            <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md mt-4">
                <legend class="text-gray-400 px-2">{{ __('Vital signs') }}</legend>
                @foreach ($this->items as $index => $item)
                    <div class="flex justify-between items-end gap-4">
                        <div class="w-1/2">
                            <x-input-label for="type" value="{{ __('Type') }}" />
                            <x-select wire:model.live="form.vital_signs.{{ $index }}.type" id="type"
                                name="type" :options="$types" class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('form.vital_signs.{{ $index }}.type')" class="mt-2" />
                        </div>
                        <div class="w-1/2">
                            <x-input-label for="value" value="{{ __('Value') }}" />
                            <x-text-input wire:model.live="form.vital_signs.{{ $index }}.value" id="value"
                                name="value" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('form.vital_signs.{{ $index }}.value')" class="mt-2" />
                        </div>
                        <div class="flex-1 items-end">

                            <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                                wire:click="remove('{{ $index }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-5 h-5">
                                    <path
                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
                <div class="w-full mt-2">
                    <x-secondary-button wire:click="add">
                        {{ __('Add  Vital Signs') }}
                    </x-secondary-button>
                </div>
            </fieldset>
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
