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

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Med Representatives') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="min-w-full">
                    <div class="space-y-6">
                        <div class="flex justify-between">
                            <x-text-input wire:model.live="search" class="py-2" type="search" :placeholder="__('Search Med Rep...')" />
                            <x-secondary-button wire:click="create">
                                {{ __('Create New') }}
                            </x-secondary-button>
                        </div>

                        <div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg">
                            <x-table for="med-representative">
                                <x-table.thead>
                                    <x-table.row class="">
                                        <x-table.thead-cell :title="__('Full Name')" class="text-left" />
                                        <x-table.thead-cell :title="__('Phone Number')" class="text-center" />
                                        <x-table.thead-cell :title="__('Gender')" class="text-center" />
                                        <x-table.thead-cell title="" class="text-right" />
                                    </x-table.row>
                                </x-table.thead>
                                <x-table.tbody class="">
                                    @forelse ($medRepresentatives as $medRepresentative)
                                        <x-table.row class="bg-white "
                                            wire:loading.class="opacity-50">
                                            <x-table.tbody-cell :item="$medRepresentative->full_name" />
                                            <x-table.tbody-cell :item="$medRepresentative->phone_number" class="text-center" />
                                            <x-table.tbody-cell :item="$medRepresentative->gender" class="text-center uppercase" />
                                            <x-table.tbody-cell :item="$medRepresentative->id" class="text-right"
                                                :action="true">
                                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                                    wire:click="detail({{ $medRepresentative }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                                                    </svg>

                                                </button>
                                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                                    wire:click="edit({{ $medRepresentative->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path
                                                            d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-info m-1 text-red-600 font-medium underline"
                                                    wire:click="delete({{ $medRepresentative }})"
                                                    wire:confirm="Are you sure you want to delete this patient?">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" class="w-5 h-5">
                                                        <path fill-rule="evenodd"
                                                            d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </x-table.tbody-cell>
                                        </x-table.row>
                                    @empty
                                        <x-table.row class="bg-white ">
                                            <x-table.tbody-cell colspan="6" :item="__('No med representative found!!')" />
                                        </x-table.row>
                                    @endforelse
                                </x-table.tbody>
                            </x-table>
                        </div>
                        <div>
                            {{ $medRepresentatives->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

