<?php

use App\Models\MedicalCondition;
use function Livewire\Volt\{state};

state([
    'medical_conditions' => fn($patient) => MedicalCondition::where('patient_id', $patient->id)->get(),
]);
?>

<div>
    @forelse ($medical_conditions as $medical_condition)
        <p>Medical Condition {{ $medical_condition->id }}</p>

    @empty
        <p>No medical condition record</p>
    @endforelse
</div>
