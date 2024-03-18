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
    <h3 class="text-xl font-bold text-navy-700 dark:text-white">{{ __('Addresses') }}</h3>
    @if ($addresses)
        <ul class="flex min-w-[240px] flex-col gap-1 p-2 font-sans text-base font-normal text-blue-gray-700">
            @forelse ($addresses as $address)
                <li
                    class=" w-full py-1 pr-1 leading-tight transition-all rounded-lg outline-none text-start hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">
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
