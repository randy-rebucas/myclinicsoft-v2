<?php

use App\Models\Encounter;
use App\Models\Que;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Livewire\Forms\QueForm;
use function Livewire\Volt\{state, form, mount, computed};

state(['patient', 'encounterId', 'filter', 'show' => false]);

form(QueForm::class);

mount(function () {
    $this->form->que_number = $this->generateSequenceNumber('queing', [], 'SQ-', 3);
    // $this->form->metadata = $this->patient->id;
    $this->form->status = 'waiting';
    $this->form->patient_id = $this->patient->id;
});

$que = computed(function () {
    return Que::where('patient_id', $this->patient->id)
        ->latest()
        ->first();
});

$create = function () {
    $this->form->store();

    $this->form->empty();
};

$delete = function (Que $que) {
    $que->delete();

    $this->dispatch('refresh');
};

// $filterDate = function (Encounter $encounter) {
//     $this->filter = $encounter->encounter_date;
//     $this->show = false;
//     $this->dispatch('set-encounter', encounterId: $encounter->id);
// };

$generateSequenceNumber = function ($tablename, array $conditions = [], string $prefix, int $length = 5) {
    $model = DB::table($tablename);

    if (is_array($conditions) && count($conditions) > 0) {
        $model = $model->where($conditions);
    }

    return $prefix . str_pad($model->count() + 1, $length, '0', STR_PAD_LEFT);
};
?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md" wire:loading.class="opacity-50">
        <legend class="dark:text-gray-200 px-2">{{ __('Basic Information') }}</legend>

        <div class="align-center flex gap-4 justify-between">
            <p class="text-xl font-bold text-navy-700 dark:text-white">{{ $this->que ? $this->que->que_number : '' }}</p>
            @if ($this->que)
                <x-danger-button class="ms-3 py-3" wire:click="delete('{{ $this->que->id }}')">
                    {{ __('Remove from waiting list') }}
                </x-danger-button>
            @else
                <x-secondary-button class="ms-3 py-3" wire:click="create">
                    {{ __('Move to waiting list') }}
                </x-secondary-button>
            @endif
        </div>
        
        @if ($this->que)
            <livewire:patient.encounter.physical-examination :patientId="$this->patient->id" />
        @endif
    </fieldset>

</div>
