<?php

use function Livewire\Volt\{state};

state('patient');

?>

<div
    class="relative flex flex-col items-center rounded-[20px] w-[400px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 ">
    <div class="relative flex h-32 w-full justify-center rounded-xl bg-cover">
        <img src='https://horizon-tailwind-react-git-tailwind-components-horizon-ui.vercel.app/static/media/banner.ef572d78f29b0fee0a09.png'
            class="absolute flex h-32 w-full justify-center rounded-xl bg-cover">
        <div
            class="absolute -bottom-12 flex h-[87px] w-[87px] items-center justify-center rounded-full border-[4px] border-white bg-pink-400 ">
            @if ($patient->avatar)
                <img class="h-full w-full rounded-full" src="{{ asset('storage/' . $patient->avatar) }}"
                    :alt="$patient->full_name"/>
            @else
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            @endif
        </div>
    </div>
    <div class="mt-16 flex flex-col items-center">
        <h4 class="text-xl font-bold text-navy-700 ">
            {{ $patient->full_name }}
        </h4>
        <p class="text-base font-normal text-gray-600">{{ $patient->phone_number }}</p>
    </div>
    <div class="mt-6 mb-3 flex gap-14 md:!gap-14">
        <div class="flex flex-col items-center justify-center">
            <p class="text-2xl font-bold text-navy-700 ">{{ $patient->gender }}
            </p>
            <p class="text-sm font-normal text-gray-600">Gender</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <p class="text-2xl font-bold text-navy-700 ">
                {{ $patient->date_of_birth }}
            </p>
            <p class="text-sm font-normal text-gray-600">Birth Date</p>
        </div>
        <div class="flex flex-col items-center justify-center">
            <p class="text-2xl font-bold text-navy-700 ">
                {{ $patient->age }}
            </p>
            <p class="text-sm font-normal text-gray-600">Age</p>
        </div>
    </div>
</div>
