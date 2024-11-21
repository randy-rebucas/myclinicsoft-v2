<?php

use Faker\Generator as Faker;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;
use function Livewire\Volt\{layout, state, on, form, mount, with, usesPagination};

form(PatientForm::class);

state('patient');

state([
    'search',
    'genders' => fn() => [
        'male' => 'Male',
        'female' => 'Female',
        'unknown' => 'Unknown',
    ],
]);

layout('layouts.app');

usesPagination();

with(fn() => ['patients' => Patient::where('first_name', 'like', '%' . $this->search . '%')->paginate(10)]);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName();
    $this->form->email = $faker->unique()->email();
    $this->form->password = Hash::make('password');
    $this->form->first_name = $faker->firstName();
    $this->form->last_name = $faker->lastName();
});

$delete = function (Patient $patient) {
    $patient->delete();

    $this->dispatch('refresh');
};

$detail = function (Patient $patient) {
    $this->redirectRoute('patient-detail', ['patientId' => $patient]);
};

$edit = function ($id) {
    $this->patient = Patient::find($id);
    $this->form->setPatient($this->patient);
    $this->dispatch('open-modal', 'form-patient');
};

$create = function () {
    $this->patient = null;
    $this->form->clearInputs();
    $this->dispatch('open-modal', 'form-patient');
};


$save = function () {
    $this->form->store($this->patient);

    $this->dispatch('close-modal', 'form-patient');

    $this->dispatch('refresh');
};

on(['set-date' => function ($date) {
    $this->form->date_of_birth = $date['date'];
}]);

?>
<section>
    <div class="max-w-7xl mx-auto">
        <div class="space-y-6">
            <div class="flex justify-between">
                <x-text-input wire:model.live="search" class="py-2" type="search" :placeholder="__('Search Patient...')" />
                <x-secondary-button wire:click="create">
                    {{ __('Create New') }}
                </x-secondary-button>
            </div>

            <div class="overflow-hidden">
                <div class="grid grid-cols-1 gap-2">
                    @forelse ($patients as $patient)
                        <div class="cursor-pointer bg-white p-2 rounded-lg shadow hover:shadow-md transition-shadow group" wire:click="detail({{ $patient }})">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img class="h-12 w-12 rounded-full object-cover"
                                         src="{{ $patient->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($patient->full_name) }}"
                                         alt="{{ $patient->full_name }}">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $patient->full_name }}
                                            </p>
                                            @if($patient->height || $patient->weight)
                                                <span class="text-sm text-gray-500">
                                                    ({{ $patient->height ? 'H: '.$patient->height . 'cm' : '' }}{{ $patient->height && $patient->weight ? ' / ' : '' }}{{ $patient->weight ? 'W: '.$patient->weight . 'kg' : '' }})
                                                </span>
                                            @endif
                                        </div>
                                        <div x-data="{ open: false }" class="relative">
                                            <button type="button"
                                                @click.stop="open = !open"
                                                class="text-gray-600 hover:text-gray-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                                </svg>
                                            </button>

                                            <div x-show="open"
                                                @click.away="open = false"
                                                class="absolute -top-2 right-8 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                                <button type="button"
                                                    wire:click="edit({{ $patient->id }})"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                    wire:click="delete({{ $patient }})"
                                                    wire:confirm="Are you sure you want to delete this patient?"
                                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-4 gap-4 mt-2 text-sm text-gray-500">
                                        <div>
                                            <span class="font-medium">Phone:</span> {{ $patient->phone_number }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Gender:</span> {{ strtoupper($patient->gender) }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Birthdate:</span> {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Age:</span> {{ $patient->age }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-4 rounded-lg shadow text-center text-gray-500">
                            {{ __('No patient found!!') }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div>
                {{ $patients->links() }}
            </div>
        </div>
    </div>


    <x-modal name="form-patient" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Patient Form') }}
            </h2>

            <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
                <legend class="text-gray-400 px-2">{{ __('Personal Details') }}</legend>
                <div class="flex justify-between gap-4">
                    <div class="w-1/2">
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input wire:model="form.first_name" id="first_name" class="block mt-1 w-full"
                            type="text" name="first_name" autofocus />
                        <x-input-error :messages="$errors->get('form.first_name')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <x-text-input wire:model="form.last_name" id="last_name" class="block mt-1 w-full"
                            type="text" name="last_name" />
                        <x-input-error :messages="$errors->get('form.last_name')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-between gap-4 mt-4">
                    <div class="w-1/2">
                        <x-input-label for="height" :value="__('Height (cm)')" />
                        <x-text-input wire:model="form.height" id="height" class="block mt-1 w-full"
                            type="number" name="height" />
                        <x-input-error :messages="$errors->get('form.height')" class="mt-2" />
                    </div>
                    <div class="w-1/2">
                        <x-input-label for="weight" :value="__('Weight (kg)')" />
                        <x-text-input wire:model="form.weight" id="weight" class="block mt-1 w-full"
                            type="number" name="weight" />
                        <x-input-error :messages="$errors->get('form.weight')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-between gap-4 mt-4">
                    <div class="w-1/2">
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input wire:model="form.phone_number" id="phone_number" class="block mt-1 w-full"
                            type="text" name="phone_number" />
                        <x-input-error :messages="$errors->get('form.phone_number')" class="mt-2" />
                    </div>
                    <div class="w-1/2">
                        <x-input-label for="gender" :value="__('Gender')" />
                        <x-select wire:model="form.gender" id="gender" name="gender" :options="$genders"
                            class="block mt-1 w-full" />
                        <x-input-error :messages="$errors->get('form.gender')" class="mt-2" />
                    </div>
                </div>
            </fieldset>

            <fieldset class="mt-6 border-2 border-double border-gray-200 p-4 rounded-md hidden">
                <legend class="text-gray-400 px-2">{{ __('Auth Credentials') }}</legend>
                <div class="flex justify-between gap-4">
                    <div class="w-1/2">
                        <x-input-label for="name" :value="__('Username')" />
                        <x-text-input wire:model="form.name" id="name" class="block mt-1 w-full bg-gray-100"
                            type="text" name="name" autofocus autocomplete="username" readonly />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="w-1/2">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full bg-gray-100"
                            type="email" name="email" readonly />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />

                    <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full bg-gray-100"
                        type="password" name="password" autocomplete="new-password" readonly />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </fieldset>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

