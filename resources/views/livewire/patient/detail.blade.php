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
            {{ __($patient->full_name . ' Informations') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex gap-14">
                    <div>
                        <livewire:patient.profile :patient="$patient" />
                        <livewire:patient.address :patient="$patient" />
                    </div>
                    <div class="flex-1">
                        <livewire:patient.encounter.index :patient="$patient" />
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <livewire:patient.record.index :patient="$patient" />
            </div>
        </div>
    </div>
</section>
