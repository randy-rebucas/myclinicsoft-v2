<?php

use App\Models\PhysicalExamination;
use function Livewire\Volt\{state};

state([
    'physical_exam' => fn($encounter) => PhysicalExamination::where('encounter_id', $encounter->id)->first(),
]);
?>

<div>

    <p>Physical Examination {{ $physical_exam ? $physical_exam->general_appearance : '--' }}</p>

</div>
