<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

?>

<section>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <ul class="" x-data="{ selected: 0 }">
                    <li class="flex align-center flex-col">
                        <h4 @click="selected !== 0 ? selected = 0 : selected = null"
                            class="bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 rounded-t text-white">
                            Business Details</h4>
                        <div x-show="selected == 0" class="border py-4 px-2">
                            <livewire:setting.form.business />
                        </div>
                    </li>

                    <li class="flex align-center flex-col">
                        <h4 @click="selected !== 1 ? selected = 1 : selected = null"
                            :class="{
                                'bg-gray-500 cursor-pointer hover:opacity-75 inline-block px-5 py-3 text-white': true,
                                'rounded-b': selected !=
                                    1
                            }">
                            Licenses</h4>
                        <div x-show="selected == 1" :class="{ 'border py-4 px-2': true, 'rounded-b': selected == 1 }">
                            <livewire:setting.form.license />
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
