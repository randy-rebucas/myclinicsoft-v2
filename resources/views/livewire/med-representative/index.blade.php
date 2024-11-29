<?php

use Faker\Generator as Faker;
use App\Models\MedRepresentative;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\MedRepresentativeForm;
use function Livewire\Volt\{layout, state, on, form, mount, with, usesPagination};

form(MedRepresentativeForm::class);

state('medRepresentative');

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

with(fn() => ['medRepresentatives' => MedRepresentative::where('first_name', 'like', '%' . $this->search . '%')->paginate(10)]);

mount(function (Faker $faker) {
    $this->form->name = $faker->userName();
    $this->form->email = $faker->unique()->email();
    $this->form->password = Hash::make('password');
});

$delete = function (MedRepresentative $medRepresentative) {
    $medRepresentative->delete();

    $this->dispatch('refresh');
};

$detail = function (MedRepresentative $medRepresentative) {
    $this->redirectRoute('med-representative-detail', ['medRepresentativeId' => $medRepresentative]);
};

$edit = function ($id) {
    $this->medRepresentative = MedRepresentative::findOrFail($id);

    $this->form->setMedRepresentative($this->medRepresentative);

    $this->dispatch('open-modal', 'form-med-representative');
};

$create = function () {
    $this->medRepresentative = null;

    $this->dispatch('open-modal', 'form-med-representative');
};

$save = function () {
    $this->form->store($this->medRepresentative);

    $this->dispatch('close-modal', 'form-med-representative');

    $this->dispatch('refresh');
};
?>

<section class="min-h-screen bg-gray-50/30 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl rounded-xl">
            <!-- Modern Top Bar -->
            <div class="border-b bg-gray-50">
                <div class="flex items-center justify-between p-6">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <x-text-input wire:model.live="search" class="ps-12 w-full py-2.5 bg-white" type="search" :placeholder="__('Search representatives...')" />
                        </div>
                    </div>
                    <x-primary-button wire:click="create" class="flex items-center gap-2 px-4 py-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Add Representative') }}
                    </x-primary-button>
                </div>
            </div>

            <!-- Modern Content Area -->
            <div class="flex-1 overflow-auto">
                <!-- Table Body -->
                <div class="divide-y divide-gray-100">
                    @forelse ($medRepresentatives as $medRepresentative)
                        <div class="group hover:bg-gray-50 transition-all duration-150">
                            <div class="flex items-center px-6 py-3">
                                <div class="flex-1" wire:click="detail({{ $medRepresentative }})">
                                    <div class="font-medium text-gray-900">{{ $medRepresentative->full_name }}</div>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mt-0.5">
                                        <span>{{ $medRepresentative->phone_number }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $medRepresentative->gender === 'male' ? 'bg-blue-50 text-blue-700' :
                                               ($medRepresentative->gender === 'female' ? 'bg-pink-50 text-pink-700' : 'bg-gray-50 text-gray-700') }}">
                                            {{ Str::ucfirst($medRepresentative->gender) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Modern Actions -->
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <button wire:click.stop="edit({{ $medRepresentative->id }})"
                                        class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="delete({{ $medRepresentative }})"
                                        wire:confirm="Are you sure you want to delete this representative?"
                                        class="p-2 hover:bg-red-50 rounded-lg text-gray-500 hover:text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ __('No medical representatives found') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modern Pagination -->
            <div class="border-t px-6 py-4 bg-gray-50">
                {{ $medRepresentatives->links() }}
            </div>
        </div>
    </div>
    <!-- Keep existing modal -->
    <x-modal name="form-med-representative" :show="$errors->isNotEmpty()" focusable>
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

</section>
