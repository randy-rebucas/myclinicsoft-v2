<?php

use Illuminate\Support\Facades\DB;
use App\Models\DiagnosticTest;
use function Livewire\Volt\{state};

state([
    'diagnostic_test' => fn($encounter) => DiagnosticTest::where('encounter_id', $encounter->id)->first(),
]);

?>

<div>
    <p>Diagnostic Test {{ $diagnostic_test ? $diagnostic_test->id : '--' }}</p>
</div>
