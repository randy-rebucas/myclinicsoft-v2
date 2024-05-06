<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setup') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <p>Setup</p>
    </div>
</div>
