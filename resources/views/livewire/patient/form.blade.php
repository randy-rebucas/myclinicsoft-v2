<?php

use Faker\Generator as Faker;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;
use function Livewire\Volt\{layout, state, form, mount};

layout('layouts.app');

state(['patient'])->url();

state([
    'genders' => fn() => [
        'male' => 'Male',
        'female' => 'Female',
        'unknown' => 'Unknown',
    ],
]);

state([
    'title' => fn() => $this->patient ? 'Edit' : 'Create New',
]);

form(PatientForm::class);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName;
    $this->form->email = $faker->unique()->email;
    $this->form->password = Hash::make('password');

    if ($this->patient) {
        $patient = Patient::find($this->patient);
        $this->form->setPatient($patient);
    }
});

$save = function () {
    $this->form->store();

    $this->dispatch('patient-created');
};

$goback = function () {
    $this->redirect('/patients', navigate: true);
};

?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($title . '  Patient') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="min-w-full">

                    <form wire:submit="save">
                        <div class="flex gap-4 justify-between">
                            <div class="flex-1">
                                <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
                                    <legend class="dark:text-gray-200 px-2">{{ __('Personal Details') }}</legend>
                                    <div class="flex justify-between gap-4">
                                        <div class="w-1/2">
                                            <x-input-label for="first_name" :value="__('First Name')" />
                                            <x-text-input wire:model="form.first_name" id="first_name"
                                                class="block mt-1 w-full" type="text" name="first_name" autofocus />
                                            <x-input-error :messages="$errors->get('form.first_name')" class="mt-2" />
                                        </div>

                                        <div class="w-1/2">
                                            <x-input-label for="last_name" :value="__('Last Name')" />
                                            <x-text-input wire:model="form.last_name" id="last_name"
                                                class="block mt-1 w-full" type="text" name="last_name" />
                                            <x-input-error :messages="$errors->get('form.last_name')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex justify-between gap-4 mt-4">
                                        <div class="w-1/3">
                                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                                            <x-text-input wire:model="form.phone_number" id="phone_number"
                                                class="block mt-1 w-full" type="text" name="phone_number" />
                                            <x-input-error :messages="$errors->get('form.phone_number')" class="mt-2" />
                                        </div>
                                        <div class="w-1/3">
                                            <x-input-label for="date_of_birth" :value="__('Birth Date')" />
                                            <x-text-input wire:model="form.date_of_birth" id="date_of_birth"
                                                class="block mt-1 w-full" type="text" name="date_of_birth" />
                                            <x-input-error :messages="$errors->get('form.date_of_birth')" class="mt-2" />
                                        </div>
                                        <div class="w-1/3">
                                            <x-input-label for="gender" :value="__('Gender')" />
                                            <x-select wire:model="form.gender" id="gender" name="gender"
                                                :options="$genders" class="block mt-1 w-full" />
                                            <x-input-error :messages="$errors->get('form.gender')" class="mt-2" />
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div>
                                <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
                                    <legend class="dark:text-gray-200 px-2">{{ __('Auth Credentials') }}</legend>
                                    <div class="flex justify-between gap-4">
                                        <div class="w-1/2">
                                            <x-input-label for="name" :value="__('Username')" />
                                            <x-text-input wire:model="form.name" id="name"
                                                class="block mt-1 w-full bg-gray-100" type="text" name="name"
                                                autofocus autocomplete="username" readonly />
                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                        </div>

                                        <!-- Email Address -->
                                        <div class="w-1/2">
                                            <x-input-label for="email" :value="__('Email')" />
                                            <x-text-input wire:model="form.email" id="email"
                                                class="block mt-1 w-full bg-gray-100" type="email" name="email"
                                                readonly />
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                    </div>
                                    <!-- Password -->
                                    <div class="mt-4">
                                        <x-input-label for="password" :value="__('Password')" />

                                        <x-text-input wire:model="form.password" id="password"
                                            class="block mt-1 w-full bg-gray-100" type="password" name="password"
                                            autocomplete="new-password" readonly />

                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="flex items-center justify-between py-6">
                            <div class="notification-state">
                                <x-action-message class="me-3" on="patient-created">
                                    {{ __('Patient successfully created.') }}
                                </x-action-message>
                            </div>
                            <div class="action-buttons">
                                <x-secondary-button class="ms-3" wire:click="goback">
                                    {{ __('Go Back') }}
                                </x-secondary-button>
                                <x-primary-button class="ms-3" wire:loading.attr="disabled">
                                    {{ __('Submit') }}
                                </x-primary-button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            var picker = new Pikaday({
                field: document.getElementById('date_of_birth'),
                format: 'D/M/YYYY',
                toString(date, format) {
                    // you should do formatting based on the passed format,
                    // but we will just return 'D/M/YYYY' for simplicity
                    const day = date.getDate();
                    const month = date.getMonth() + 1;
                    const year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                },
                onSelect: function() {
                    @this.set('form.date_of_birth', picker.toString());
                }
            });
        </script>
    @endpush
</section>
