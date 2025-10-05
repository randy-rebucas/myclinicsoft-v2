<?php

use function Livewire\Volt\{state, mount};

state([
    'addresses' => fn($patient) => $patient->addresses,
]);

?>

<div>
    <h3 class="text-xl font-bold text-navy-700">{{ __('Addresses') }}</h3>
    @if ($addresses)
        <ul class="flex min-w-[240px] flex-col gap-1 p-2 font-sans text-base font-normal text-blue-gray-700">
            @forelse ($addresses as $address)
                <li
                    class=" w-full py-1 pr-1 leading-tight transition-all rounded-lg outline-none text-start hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">
                    <address>
                        {{ $address->address_line_1 . ' ' . $address->address_line_2 }},
                        {{ $address->city }}, {{ $address->state }} <br />
                        {{ $address->country . ' ' . $address->postal_code }}
                    </address>
                </li>
            @empty
                <li class="bg-white">
                    {{ __('No patient address found!!') }}
                </li>
            @endforelse
        </ul>
    @endif
</div>
