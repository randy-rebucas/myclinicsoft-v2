<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;

use function Livewire\Volt\{state, form, mount, computed, with, usesPagination};

form(PatientForm::class);

state([
    'genders' => fn() => [
        'male' => 'Male',
        'female' => 'Female',
        'unknown' => 'Unknown',
    ],
]);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName();
    $this->form->email = $faker->unique()->email();
    $this->form->password = Hash::make('password');
    $this->form->first_name = $faker->firstName();
    $this->form->last_name = $faker->lastName();
});

$save = function () {
    $this->form->store(null);

    $this->dispatch('close-create-patient-modal');
    $this->dispatch('set-patient', $this->form->newPatient->id);
};

?>

<div>
    <form wire:submit="save">
        <div class="w-full mb-6">
            <x-input-label for="first_name" :value="__('First Name')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input wire:model.live="form.first_name" id="first_name" class="block mt-1 w-full" type="text"
                name="first_name" autofocus />
            <x-input-error :messages="$errors->get('form.first_name')" class="mt-2" />
        </div>

        <div class="w-full mb-6">
            <x-input-label for="last_name" :value="__('Last Name')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input wire:model.live="form.last_name" id="last_name" class="block mt-1 w-full" type="text"
                name="last_name" />
            <x-input-error :messages="$errors->get('form.last_name')" class="mt-2" />
        </div>

        <div class="w-full mb-6">
            <x-input-label for="height" :value="__('Height (cm)')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input wire:model.live="form.height" id="height" class="block mt-1 w-full" type="number"
                name="height" />
            <x-input-error :messages="$errors->get('form.height')" class="mt-2" />
        </div>
        <div class="w-full mb-6">
            <x-input-label for="weight" :value="__('Weight (kg)')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input wire:model.live="form.weight" id="weight" class="block mt-1 w-full" type="number"
                name="weight" />
            <x-input-error :messages="$errors->get('form.weight')" class="mt-2" />
        </div>

        <div class="w-full mb-6">
            <x-input-label for="phone_number" :value="__('Phone Number')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input wire:model.live="form.phone_number" id="phone_number" class="block mt-1 w-full" type="text"
                name="phone_number" />
            <x-input-error :messages="$errors->get('form.phone_number')" class="mt-2" />
        </div>
        <div class="w-full mb-6">
            <x-input-label for="gender" :value="__('Gender')"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-select wire:model.live="form.gender" id="gender" name="gender" :options="$genders"
                class="block mt-1 w-full" />
            <x-input-error :messages="$errors->get('form.gender')" class="mt-2" />
        </div>

        <fieldset class="mt-6 border-2 border-double border-gray-200 p-4 rounded-md hidden">
            <legend class="text-gray-400 px-2">{{ __('Auth Credentials') }}</legend>
            <div class="flex justify-between gap-4">
                <div class="w-1/2">
                    <x-input-label for="name" :value="__('Username')" />
                    <x-text-input wire:model.live="form.name" id="name" class="block mt-1 w-full bg-gray-100"
                        type="text" name="name" autofocus autocomplete="username" readonly />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="w-1/2">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model.live="form.email" id="email" class="block mt-1 w-full bg-gray-100"
                        type="email" name="email" readonly />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>
            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input wire:model.live="form.password" id="password" class="block mt-1 w-full bg-gray-100"
                    type="password" name="password" autocomplete="new-password" readonly />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </fieldset>
        <div class="pt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Add Patient') }}
            </x-primary-button>
        </div>
        <!-- Modal Backdrop -->
    </form>
</div>
