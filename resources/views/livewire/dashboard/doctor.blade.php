<?php

use App\Models\Queue;
use function Livewire\Volt\{state, on};
state([
    'selectedQueue' => null,
]);

on([
    'selected-queue' => function ($queueId) {
        $this->selectedQueue = Queue::findOrFail($queueId);
    },
    'queue-completed' => function () {
        $this->selectedQueue = null;
    },
]);

?>

<div class="bg-gray-50 min-h-screen py-8">
    @if ($selectedQueue)
        <livewire:patient.record :queue="$this->selectedQueue" />
    @else
        <livewire:doctor.index />
    @endif
</div>
