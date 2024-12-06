<?php

use function Livewire\Volt\{state, mount};

state(['record']);

mount(function ($record) {
    $this->record = $record;
});

?>

<dl class="divide-y divide-gray-200">
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Allergen</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $record->allergen }}</dd>
    </div>
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Reaction</dt>
        <dd class="mt-1 text-sm text-gray-900">{{ $record->reaction }}</dd>
    </div>
    <div class="py-4">
        <dt class="text-sm font-medium text-gray-500">Severity</dt>
        <dd class="mt-1 text-sm text-gray-900">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $record->severity === 'High'
                    ? 'bg-red-100 text-red-800'
                    : ($record->severity === 'Medium'
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-green-100 text-green-800') }}">
                {{ $record->severity }}
            </span>
        </dd>
    </div>
</dl>
