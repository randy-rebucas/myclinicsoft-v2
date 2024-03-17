<?php

use App\Models\Immunization;
use function Livewire\Volt\{state};

state([
    'immunizations' => fn($patient) => Immunization::where('patient_id', $patient->id)->get(),
]);
?>

<div>
    @forelse ($immunizations as $immunization)
        <p>Immunization {{ $immunization->id }}</p>
    @empty
        <p>No Immunization record</p>
    @endforelse
</div>
