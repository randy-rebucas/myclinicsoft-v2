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

            <div class="overflow-x-auto border rounded-lg">
                <x-table for="patient" class="min-w-full divide-y divide-gray-200">
                    <x-table.thead>
                        <x-table.row class="bg-gray-50">
                            <x-table.thead-cell :title="__('Full Name')" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase" />
                            <x-table.thead-cell :title="__('Phone Number')" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase" />
                            <x-table.thead-cell :title="__('Gender')" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase" />
                            <x-table.thead-cell :title="__('Birthdate')" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase" />
                            <x-table.thead-cell :title="__('Age')" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase" />
                            <x-table.thead-cell title="" class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase" />
                        </x-table.row>
                    </x-table.thead>
                    <x-table.tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($patients as $patient)
                        <x-table.row class="hover:bg-gray-50" wire:loading.class="opacity-50">
                            <x-table.tbody-cell :item="$patient->full_name" class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" />
                            <x-table.tbody-cell :item="$patient->phone_number" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center" />
                            <x-table.tbody-cell :item="$patient->gender" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center uppercase" />
                            <x-table.tbody-cell :item="$patient->date_of_birth" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center" />
                            <x-table.tbody-cell :item="$patient->age" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-center" />
                            <x-table.tbody-cell :item="$patient->id" class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 text-right" :action="true">
                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                    wire:click="detail({{ $patient }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-info m-1 font-medium underline"
                                    wire:click="edit({{ $patient->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                        <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-info m-1 text-red-600 font-medium underline"
                                    wire:click="delete({{ $patient }})"
                                    wire:confirm="Are you sure you want to delete this patient?">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-table.tbody-cell>
                        </x-table.row>
                        @empty
                        <x-table.row class="bg-white ">
                            <x-table.tbody-cell colspan="6" :item="__('No patient found!!')" />
                        </x-table.row>
                        @endforelse
                    </x-table.tbody>
                </x-table>
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
                    <div class="w-1/3">
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input wire:model="form.phone_number" id="phone_number" class="block mt-1 w-full"
                            type="text" name="phone_number" />
                        <x-input-error :messages="$errors->get('form.phone_number')" class="mt-2" />
                    </div>
                    <div class="w-1/3">
                        <x-input-label for="date_of_birth" :value="__('Birth Date')" />
                        <x-text-input wire:model="form.date_of_birth" id="date_of_birth" class="block mt-1 w-full"
                            type="date" name="date_of_birth" />
                        <x-input-error :messages="$errors->get('form.date_of_birth')" class="mt-2" />
                    </div>
                    <div class="w-1/3">
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
    @push('scripts')
    <script>
        var picker = new Pikaday({
            field: document.getElementById('date_of_birth'),
            format: 'YYYY-MM-DD',
            toString(date, format) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${year}-${month}-${day}`;
            },
            onSelect: function() {
                Livewire.dispatch('set-date', {
                    date: picker.toString()
                });
            }
        });
    </script>
    @endpush
