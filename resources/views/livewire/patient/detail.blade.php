<?php

use Faker\Generator as Faker;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;
use function Livewire\Volt\{layout, state, form, mount, title};

state(['patientId'])->url();

state([
    'patient' => fn() => Patient::find($this->patientId),
]);

layout('layouts.app');

$goback = function () {
    $this->redirect('/patients', navigate: true);
};

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($patient->full_name) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="min-w-full">
                    <div class="flex gap-14">
                        <div
                            class="relative flex flex-col items-center rounded-[20px] w-[400px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:!shadow-none">
                            <div class="relative flex h-32 w-full justify-center rounded-xl bg-cover">
                                <img src='https://horizon-tailwind-react-git-tailwind-components-horizon-ui.vercel.app/static/media/banner.ef572d78f29b0fee0a09.png'
                                    class="absolute flex h-32 w-full justify-center rounded-xl bg-cover">
                                <div
                                    class="absolute -bottom-12 flex h-[87px] w-[87px] items-center justify-center rounded-full border-[4px] border-white bg-pink-400 dark:!border-navy-700">
                                    <img class="h-full w-full rounded-full"
                                        src='https://horizon-tailwind-react-git-tailwind-components-horizon-ui.vercel.app/static/media/avatar11.1060b63041fdffa5f8ef.png'
                                        alt="" />
                                </div>
                            </div>
                            <div class="mt-16 flex flex-col items-center">
                                <h4 class="text-xl font-bold text-navy-700 dark:text-white">
                                    {{ $patient->full_name }}
                                </h4>
                                <p class="text-base font-normal text-gray-600">{{ $patient->phone_number }}</p>
                            </div>
                            <div class="mt-6 mb-3 flex gap-14 md:!gap-14">
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-2xl font-bold text-navy-700 dark:text-white">{{ $patient->gender }}
                                    </p>
                                    <p class="text-sm font-normal text-gray-600">Gender</p>
                                </div>
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-2xl font-bold text-navy-700 dark:text-white">
                                        {{ $patient->date_of_birth }}
                                    </p>
                                    <p class="text-sm font-normal text-gray-600">Birth Date</p>
                                </div>
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-2xl font-bold text-navy-700 dark:text-white">
                                        {{ $patient->age }}
                                    </p>
                                    <p class="text-sm font-normal text-gray-600">Age</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <livewire:patient.address :patient="$patient" />

                            <livewire:patient.encounter.index :patient="$patient" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:patient.record.index :patient="$patient" />
                </div>
            </div>
        </div>
    </div>
</section>
