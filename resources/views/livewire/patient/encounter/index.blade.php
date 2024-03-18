<?php

use App\Models\Encounter;
use function Livewire\Volt\{state};

state([
    'encounter' => fn($patient) => Encounter::where('patient_id', $patient->id)->first(),
]);

?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
        <legend class="dark:text-gray-200 px-2">{{ __('Latest Encounter') }}</legend>
        <livewire:patient.encounter.physical-examination :encounter="$encounter" />
        <livewire:patient.encounter.diagnostic-test :encounter="$encounter" />
    </fieldset>
</div>
