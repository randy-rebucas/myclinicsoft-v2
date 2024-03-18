<?php

use function Livewire\Volt\{state, mount};

state('patient');

?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
        <legend class="dark:text-gray-200 px-2">{{ __('Record') }}</legend>
        <livewire:patient.record.medical-condition :patient="$patient" />
        <livewire:patient.record.medication :patient="$patient" />
        <livewire:patient.record.family-history :patient="$patient" />
        <livewire:patient.record.allergy :patient="$patient" :patientId="$patient" />
        <livewire:patient.record.immunization :patient="$patient" />
    </fieldset>
</div>
