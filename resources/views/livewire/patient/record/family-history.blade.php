<?php

use App\Models\FamilyHistory;
use function Livewire\Volt\{state};

state([
    'family_histories' => fn($patient) => FamilyHistory::where('patient_id', $patient->id)->get(),
]);
?>

<div>
    @forelse ($family_histories as $family_history)
        <p>Family History {{ $family_history->id }}</p>
    @empty

        <p>No Family history</p>
    @endforelse

</div>
