<?php

use App\Models\Encounter;
use function Livewire\Volt\{state};

state([
    'encounter' => fn($patient) => Encounter::where('patient_id', $patient->id)->first(),
]);

?>

<div>

    <livewire:patient.encounter.physical-examination :encounter="$encounter" />
    <livewire:patient.encounter.diagnostic-test :encounter="$encounter" />
</div>
