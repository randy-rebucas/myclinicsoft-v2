<?php

use function Livewire\Volt\{state};

state(['patient']);
?>

<nav class="space-y-2">
    <!-- Allergies -->
    <livewire:patient.record.partials.sidebar.allergies :patient="$this->patient" />

    <!-- Family History -->
    <livewire:patient.record.partials.sidebar.family-history :patient="$this->patient" />

    <!-- Diagnostic Tests -->
    <livewire:patient.record.partials.sidebar.diagnostic-tests :patient="$this->patient" />

    <!-- Immunizations -->
    <livewire:patient.record.partials.sidebar.immunizations :patient="$this->patient" />

    <!-- Medical Conditions -->
    <livewire:patient.record.partials.sidebar.medical-conditions :patient="$this->patient" />

    <!-- Vitals -->
    <livewire:patient.record.partials.sidebar.vitals :patient="$this->patient" />
</nav>
