<?php

use App\Models\Medication;
use function Livewire\Volt\{state};

state([
    'medications' => fn($patient) => Medication::where('patient_id', $patient->id)->get(),
]);
?>

<div>
    @forelse ($medications as $medication)
        <p>Medication {{ $medication->id }}</p>
    @empty
        <p>No medication record</p>
    @endforelse
</div>
