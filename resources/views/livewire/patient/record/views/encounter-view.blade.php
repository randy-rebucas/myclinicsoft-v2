<?php

use function Livewire\Volt\{state, mount};

state(['record']);

mount(function ($record) {
    $this->record = $record;
});

?>

<dl class="divide-y divide-gray-200">
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Encounter Date</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $record->encounter_date }}</dd>
    </div>
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Chief Complaint</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $record->chief_complaint }}</dd>
    </div>
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Notes</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $record->notes }}</dd>
    </div>
</dl>
