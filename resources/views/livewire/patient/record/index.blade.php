<?php

use function Livewire\Volt\{state};

state('patient');

?>

<div>
    <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
        <legend class="dark:text-gray-200 px-2">{{ __('Record') }}</legend>
        <ul class="" x-data="{ selected: 0 }">
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 0 ? selected = 0 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 rounded-t text-white">
                    Encounters</h4>
                <div x-show="selected == 0" class="border py-4 px-2">
                    <livewire:patient.record.encounter :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 1 ? selected = 1 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white">
                    Medications</h4>
                <div x-show="selected == 1" class="border py-4 px-2">
                    <livewire:patient.record.medication :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 2 ? selected = 2 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white">
                    Medical Conditions</h4>
                <div x-show="selected == 2" class="border py-4 px-2">
                    <livewire:patient.record.medical-condition :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 3 ? selected = 3 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white">
                    Family histories</h4>
                <div x-show="selected == 3" class="border py-4 px-2">
                    <livewire:patient.record.family-history :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 4 ? selected = 4 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white">
                    Allergies</h4>
                <div x-show="selected == 4" class="border py-4 px-2">
                    <livewire:patient.record.allergy :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 5 ? selected = 5 : selected = null"
                    class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white">
                    Diagnostic test</h4>
                <div x-show="selected == 5" class="border py-4 px-2">
                    <livewire:patient.record.diagnostic-test :patient="$patient" />
                </div>
            </li>
            <li class="flex align-center flex-col">
                <h4 @click="selected !== 6 ? selected = 6 : selected = null"
                    :class="{
                        'bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white': true,
                        'rounded-b': selected !=
                            6
                    }">
                    Immunizations</h4>
                <div x-show="selected == 6" :class="{ 'border py-4 px-2': true, 'rounded-b': selected == 5 }">
                    <livewire:patient.record.immunization :patient="$patient" />
                </div>
            </li>
        </ul>
    </fieldset>
</div>
