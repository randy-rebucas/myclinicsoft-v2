<?php

use Faker\Generator as Faker;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\DoctorForm;
use function Livewire\Volt\{layout, state, on, form, mount, with, usesPagination};

form(DoctorForm::class);

state('doctor');

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

with(
    fn() => [
        'doctors' => Doctor::when($this->search, function ($query) {
            $query
                ->where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('phone_number', 'like', '%' . $this->search . '%');
        })->paginate(10),
    ],
);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName();
    $this->form->email = $faker->unique()->email();
    $this->form->password = Hash::make('password');
});

$delete = function (Doctor $doctor) {
    $doctor->delete();

    $this->dispatch('refresh');
};

$detail = function (Doctor $doctor) {
    $this->redirectRoute('doctor-detail', ['doctorId' => $doctor]);
};

$edit = function ($id) {
    $this->doctor = Doctor::findOrFail($id);

    $this->form->setDoctor($this->doctor);

    $this->dispatch('open-modal', 'form-doctor');
};

$create = function () {
    $this->doctor = null;

    $this->dispatch('open-modal', 'form-doctor');
};

$save = function () {
    $this->form->store($this->doctor);

    $this->dispatch('close-modal', 'form-doctor');

    $this->dispatch('refresh');
};
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <!-- Action Bar -->
            <div class="flex items-center justify-between gap-4 p-4 border-b">
                <div class="flex items-center gap-4">
                    <x-secondary-button wire:click="create" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('New Doctor') }}
                    </x-secondary-button>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <x-text-input wire:model.live="search" class="ps-10" type="search" :placeholder="__('Search Doctors...')" />
                </div>
            </div>

            <!-- List -->
            <div class="divide-y divide-gray-200">
                @forelse ($doctors as $doctor)
                    <div class="flex items-center px-4 py-3 hover:bg-gray-50 group">

                        <div class="flex-1 min-w-0 px-4 cursor-pointer" wire:click="detail({{ $doctor }})">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $doctor->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $doctor->phone_number }}</div>
                                </div>
                                <div>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium uppercase"
                                        style="background-color: {{ $doctor->gender === 'male' ? '#e0e7ff' : ($doctor->gender === 'female' ? '#fce7f3' : '#f3f4f6') }}; color: {{ $doctor->gender === 'male' ? '#4338ca' : ($doctor->gender === 'female' ? '#be185d' : '#374151') }}">
                                        {{ $doctor->gender }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-none opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="edit({{ $doctor->id }})"
                                    class="p-1 rounded-full hover:bg-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="delete({{ $doctor }})"
                                    wire:confirm="Are you sure you want to delete this doctor?"
                                    class="p-1 rounded-full hover:bg-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                        {{ __('No doctors found!!') }}
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t">
                {{ $doctors->links() }}
            </div>
        </div>
    </div>

    <x-modal name="form-doctor" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="save" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Doctor Form') }}
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
