<?php

use function Livewire\Volt\{state, mount};

state('patient');

?>

<div>
    <livewire:patient.record.family-history :patient="$patient" />
    <livewire:patient.record.immunization :patient="$patient" />
    <livewire:patient.record.medical-condition :patient="$patient" />
    <livewire:patient.record.medication :patient="$patient" />
</div>
