<?php

use App\Models\PatientAddress;
use function Livewire\Volt\{state, mount};

state([
    'addresses' => fn($patient) => PatientAddress::with('address')
        ->where('patient_id', $patient->id)
        ->get(),
]);

?>

<div>
    @if ($addresses)
        <ul>
            @forelse ($addresses as $address)
                <li>
                    <address>
                        {{ $address->address->line_1 . ' ' . $address->address->line_2 }},
                        {{ $address->address->district }} <br />
                        {{ $address->address->city->name . ', ' . $address->address->city->country->name . ' ' . $address->address->postal_code }}
                    </address>
                </li>
            @empty
                <li class="bg-white dark:bg-gray-700 dark:text-white">
                    {{ __('No patient address found!!') }}
                </li>
            @endforelse
        </ul>
    @endif
</div>
