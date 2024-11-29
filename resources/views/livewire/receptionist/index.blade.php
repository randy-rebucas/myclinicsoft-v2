<?php

use Faker\Generator as Faker;
use App\Models\Receptionist;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\ReceptionistForm;
use function Livewire\Volt\{layout, state, on, form, mount, with, usesPagination};

form(ReceptionistForm::class);

state('receptionist');

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

with(fn() => ['receptionists' => Receptionist::where('first_name', 'like', '%' . $this->search . '%')->paginate(10)]);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName();
    $this->form->email = $faker->unique()->email();
    $this->form->password = Hash::make('password');
});

$delete = function (Receptionist $receptionist) {
    $receptionist->delete();

    $this->dispatch('refresh');
};

$detail = function (Receptionist $receptionist) {
    $this->redirectRoute('receptionist-detail', ['receptionistId' => $medRepresentative]);
};

$edit = function ($id) {
    $this->receptionist = Receptionist::findOrFail($id);

    $this->form->setReceptionist($this->receptionist);

    $this->dispatch('open-modal', 'form-receptionist');
};

$create = function () {
    $this->receptionist = null;

    $this->dispatch('open-modal', 'form-receptionist');
};

$save = function () {
    $this->form->store($this->receptionist);

    $this->dispatch('close-modal', 'form-receptionist');

    $this->dispatch('refresh');
};
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <!-- Search and Create Button -->
            <div class="flex justify-between items-center mb-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <x-text-input wire:model.live="search" class="pl-10 py-2" type="search"
                        placeholder="Search receptionists..." />
                </div>
                <x-primary-button wire:click="create" class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add New') }}
                </x-primary-button>
            </div>

            <!-- Receptionist Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($receptionists as $receptionist)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200 group relative"
                        wire:loading.class="opacity-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">{{ $receptionist->full_name }}</h3>
                                <div class="mt-2 space-y-1">
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $receptionist->phone_number }}
                                    </p>
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="uppercase">{{ $receptionist->gender }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="flex gap-1 absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button wire:click="edit({{ $receptionist->id }})"
                                    class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-full transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $receptionist }})"
                                    wire:confirm="Are you sure you want to delete this receptionist?"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-full transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">No receptionist found!!</h3>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>


    <x-modal name="form-receptionist" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Med Representative Form') }}
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
</div>
