<?php

use App\Models\Patient;
use App\Models\PatientDoctor;
use Illuminate\Support\Facades\Hash;
use App\Livewire\Forms\PatientForm;
use function Livewire\Volt\{layout, state, form, mount, with, usesPagination, usesFileUploads};
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

form(PatientForm::class);

usesFileUploads();

state([
    'search',
    'patient' => null,
    'notification' => null,
    'genders' => fn() => [
        'male' => 'Male',
        'female' => 'Female',
        'unknown' => 'Unknown',
    ],
    'blood_types' => fn() => [
        'A+' => 'A+',
        'A-' => 'A-',
        'B+' => 'B+',
        'B-' => 'B-',
        'AB+' => 'AB+',
        'AB-' => 'AB-',
        'O+' => 'O+',
        'O-' => 'O-',
        'unknown' => 'Unknown',
    ],
    'importFile' => null,
]);

layout('layouts.app');

usesPagination();

with(
    fn() => [
        'patients' => PatientDoctor::with('patient')
            ->whereHas('patient', function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            })
            ->where('doctor_id', auth()->user()->doctor->id)
            ->paginate(10),
    ],
);

$delete = function (Patient $patient) {
    $patient->delete();

    $this->dispatch('refresh');
    $this->dispatch('patient-saved');
    $this->patient = null;
    $this->notification = ['type' => 'success', 'message' => 'Patient deleted successfully'];
};

$detail = function ($patientId) {
    $this->redirectRoute('patients.show', ['patient' => $patientId]);
};

$edit = function ($id) {
    $this->patient = Patient::findOrFail($id);

    $this->form->setPatient($this->patient);
    $this->dispatch('open-modal', 'form-patient');
};

$create = function () {
    $this->patient = null;
    $this->form->clearInputs();
    $this->dispatch('open-modal', 'form-patient');
};

$save = function () {
    try {
        $this->form->store($this->patient);

        $this->dispatch('close-modal', 'form-patient');

        $this->dispatch('refresh');
        $this->dispatch('patient-saved');
        
        if ($this->patient) {
            $this->notification = ['type' => 'success', 'message' => 'Patient details updated successfully'];
        } else {
            $this->notification = ['type' => 'success', 'message' => 'Patient created successfully'];
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation errors are handled by Livewire automatically
        $this->notification = ['type' => 'error', 'message' => 'Please check the form for errors'];
    } catch (\Exception $e) {
        $this->notification = ['type' => 'error', 'message' => 'Failed to save patient: ' . $e->getMessage()];
    }
};

$import = function () {
    try {
        $validated = $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:1024',
        ]);

        $path = $this->importFile->store('temp');
        $file = fopen(storage_path('app/' . $path), 'r');

        // Skip header row
        fgetcsv($file);

        DB::beginTransaction();

        while (($row = fgetcsv($file)) !== false) {
            // Validate required fields
            if (empty($row[1]) || empty($row[3])) {
                throw new \Exception('First name and last name are required');
            }

            $firstName = ucwords(trim($row[1]));
            $lastName = ucwords(trim($row[3]));
            $username = strtolower($firstName . '.' . $lastName . '.' . now()->format('His'));
            $email = $username . '@' . config('app.domain', 'example.com');

            $user = User::create([
                'name' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
            $patient = Patient::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => trim($row[9] ?? ''),
                'date_of_birth' => !empty($row[5] && $row[6] && $row[7]) ? Carbon::parse($row[5] . ' ' . $row[6] . ' ' . $row[7])->format('Y-m-d') : null,
                'height' => 0,
                'weight' => 0,
                'gender' => !empty($row[4]) ? strtolower($row[4] === '1' ? 'male' : 'female') : 'unknown',
                'user_id' => $user->id,
            ]);

            PatientDoctor::create([
                'patient_id' => $patient->id,
                'doctor_id' => auth()->user()->doctor->id,
            ]);
        }

        DB::commit();
        fclose($file);
        unlink(storage_path('app/' . $path));

        $this->importFile = null;
        $this->dispatch('refresh');
        $this->notification = ['type' => 'success', 'message' => 'Patients imported successfully'];
    } catch (\Exception $e) {
        DB::rollBack();
        if (isset($file)) {
            fclose($file);
        }
        if (isset($path)) {
            unlink(storage_path('app/' . $path));
        }
        $this->importFile = null;
        $this->notification = ['type' => 'error', 'message' => 'Failed to import patients: ' . $e->getMessage()];
    }
};

?>
<div>
    <!-- Notification Display -->
    @if ($notification)
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             class="fixed bottom-4 right-4 z-50 max-w-sm w-full">
            <div class="rounded-lg shadow-lg {{ $notification['type'] === 'success' ? 'bg-emerald-500' : 'bg-red-500' }} text-white p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if ($notification['type'] === 'success')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ $notification['message'] }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button @click="show = false" class="inline-flex text-white hover:opacity-75 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <section class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <x-text-input wire:model.live="search" class="pl-10 w-full py-2.5 bg-white" type="search"
                            :placeholder="__('Search Patient...')" />
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $patients->total() }} {{ Str::plural('patient', $patients->total()) }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form wire:submit="import" class="flex items-center">
                            <input type="file" wire:model="importFile" class="hidden" id="csvImport" accept=".csv">
                            <x-secondary-button onclick="document.getElementById('csvImport').click();" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                {{ __('Import CSV') }}
                            </x-secondary-button>
                            <x-primary-button x-show="$wire.importFile !== null" class="ml-2" type="submit">
                                {{ __('Upload') }}
                            </x-primary-button>
                        </form>
                        <x-primary-button wire:click="create" class="flex items-center gap-2 px-4 py-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('Create New') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <div class="grid grid-cols-1 gap-0.5">
                        @forelse ($patients as $patientDoctor)
                            <div class="cursor-pointer bg-white hover:shadow-md transition-all border-b group"
                                wire:click="detail({{ $patientDoctor->patient_id }})">
                                <div class="flex items-center px-4 py-3">
                                    <!-- Left side with photo -->
                                    <div class="flex-shrink-0 mr-4">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $patientDoctor->patient->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($patientDoctor->patient->full_name) }}"
                                            alt="{{ $patientDoctor->patient->full_name }}">
                                    </div>

                                    <!-- Middle content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $patientDoctor->patient->full_name }}
                                            </p>
                                            <span class="mx-2 text-gray-400">•</span>
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ $patientDoctor->patient->phone_number }}
                                            </p>
                                        </div>
                                        <div class="text-sm text-gray-500 truncate">
                                            {{ strtoupper($patientDoctor->patient->gender) }}
                                            @if ($patientDoctor->patient->date_of_birth)
                                                • {{ $patientDoctor->patient->age }} years
                                                • Born {{ $patientDoctor->patient->date_of_birth->format('M d, Y') }}
                                            @endif
                                            @if ($patientDoctor->patient->height || $patientDoctor->patient->weight)
                                                •
                                                {{ $patientDoctor->patient->height ? 'H: ' . $patientDoctor->patient->height . 'cm' : '' }}{{ $patientDoctor->patient->height && $patientDoctor->patient->weight ? ' / ' : '' }}{{ $patientDoctor->patient->weight ? 'W: ' . $patientDoctor->patient->weight . 'kg' : '' }}
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Right side actions -->
                                    <div
                                        class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click.stop="edit({{ $patientDoctor->patient_id }})"
                                            class="p-1 rounded-full hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click.stop="delete({{ $patientDoctor->patient_id }})"
                                            wire:confirm="Are you sure you want to delete this patient?"
                                            class="p-1 rounded-full hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-300 mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ __('No patients found') }}
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
            <div class="relative">
                <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $patient ? __('Edit Patient') : __('Create New Patient') }}
                </h2>

                <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md">
                    <legend class="text-gray-400 px-2">{{ __('Personal Details') }}</legend>
                    <div class="flex justify-between gap-4">
                        <div class="w-1/2">
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input wire:model.live="form.first_name" id="first_name" class="block mt-1 w-full"
                                type="text" name="first_name" autofocus />
                            <x-input-error :messages="$errors->get('form.first_name')" class="mt-2" />
                        </div>

                        <div class="w-1/2">
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input wire:model.live="form.last_name" id="last_name" class="block mt-1 w-full"
                                type="text" name="last_name" />
                            <x-input-error :messages="$errors->get('form.last_name')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex justify-between gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input-label for="height" :value="__('Height (cm)')" />
                            <x-text-input wire:model.live="form.height" id="height" class="block mt-1 w-full"
                                type="number" name="height" />
                            <x-input-error :messages="$errors->get('form.height')" class="mt-2" />
                        </div>
                        <div class="w-1/2">
                            <x-input-label for="weight" :value="__('Weight (kg)')" />
                            <x-text-input wire:model.live="form.weight" id="weight" class="block mt-1 w-full"
                                type="number" name="weight" />
                            <x-input-error :messages="$errors->get('form.weight')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex justify-between gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input wire:model.live="form.phone_number" id="phone_number"
                                class="block mt-1 w-full" type="text" name="phone_number" />
                            <x-input-error :messages="$errors->get('form.phone_number')" class="mt-2" />
                        </div>
                        <div class="w-1/2">
                            <x-input-label for="gender" :value="__('Gender')" />
                            <x-select wire:model.live="form.gender" id="gender" name="gender" :options="$genders"
                                class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('form.gender')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex justify-between gap-4 mt-4">
                        <div class="w-full">
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <x-text-input wire:model.live="form.date_of_birth" id="date_of_birth"
                                class="block mt-1 w-full" type="date" name="date_of_birth" />
                            <x-input-error :messages="$errors->get('form.date_of_birth')" class="mt-2" />
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border-2 border-double border-gray-200 p-4 rounded-md mt-6">
                    <legend class="text-gray-400 px-2">{{ __('Emergency Contact') }}</legend>
                    <div class="flex justify-between gap-4">
                        <div class="w-1/2">
                            <x-input-label for="emergency_contact_name" :value="__('Emergency Contact Name')" />
                            <x-text-input wire:model.live="form.emergency_contact_name" id="emergency_contact_name"
                                class="block mt-1 w-full" type="text" name="emergency_contact_name" />
                            <x-input-error :messages="$errors->get('form.emergency_contact_name')" class="mt-2" />
                        </div>
                        <div class="w-1/2">
                            <x-input-label for="emergency_contact_phone" :value="__('Emergency Contact Phone')" />
                            <x-text-input wire:model.live="form.emergency_contact_phone" id="emergency_contact_phone"
                                class="block mt-1 w-full" type="text" name="emergency_contact_phone" />
                            <x-input-error :messages="$errors->get('form.emergency_contact_phone')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex justify-between gap-4 mt-4">
                        <div class="w-1/2">
                            <x-input-label for="emergency_contact_relationship" :value="__('Relationship')" />
                            <x-text-input wire:model.live="form.emergency_contact_relationship" id="emergency_contact_relationship"
                                class="block mt-1 w-full" type="text" name="emergency_contact_relationship" placeholder="e.g., Spouse, Parent, Sibling" />
                            <x-input-error :messages="$errors->get('form.emergency_contact_relationship')" class="mt-2" />
                        </div>
                        <div class="w-1/2">
                            <x-input-label for="blood_type" :value="__('Blood Type')" />
                            <x-select wire:model.live="form.blood_type" id="blood_type" name="blood_type" :options="$blood_types"
                                class="block mt-1 w-full" />
                            <x-input-error :messages="$errors->get('form.blood_type')" class="mt-2" />
                        </div>
                    </div>
                </fieldset>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button wire:loading.attr="disabled" wire:target="save" class="ms-3">
                        <span wire:loading.remove wire:target="save">{{ __('Save Patient') }}</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </x-primary-button>
                </div>
                </form>
                
                <!-- Loading Overlay -->
                <div wire:loading wire:target="save" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-600">Saving...</span>
                    </div>
                </div>
            </div>
        </x-modal>
    </section>
</div>
