<?php

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Doctor;
use App\Livewire\Forms\EncounterForm;
use function Livewire\Volt\{state, form, mount, layout, title};

state(['encounter' => null]);

form(EncounterForm::class);

layout('layouts.app');

title(fn() => $this->encounter ? 'Edit Encounter' : 'Create Encounter');

mount(function (?Encounter $encounter = null) {
    $this->encounter = $encounter;
    
    if ($encounter) {
        $this->form->chief_complaint = $encounter->chief_complaint;
        $this->form->encounter_date = $encounter->encounter_date?->format('Y-m-d');
        $this->form->notes = $encounter->notes;
        $this->form->patient_id = $encounter->patient_id;
    } else {
        $this->form->encounter_date = now()->format('Y-m-d');
    }
});

$save = function () {
    if ($this->encounter) {
        $this->update();
    } else {
        $this->store();
    }
};

$store = function () {
    $this->form->validate();
    
    $encounter = Encounter::create([
        'chief_complaint' => $this->form->chief_complaint,
        'encounter_date' => $this->form->encounter_date,
        'notes' => $this->form->notes,
        'patient_id' => $this->form->patient_id,
        'doctor_id' => auth()->user()->doctor?->id,
        'status' => 'scheduled',
    ]);

    // Log encounter creation activity
    $encounter->recordActivity('created', 'Medical encounter was created');
    
    session()->flash('message', 'Encounter created successfully.');
    $this->redirect(route('encounters.show', $encounter));
};

$update = function () {
    $this->form->validate();
    
    $this->encounter->update([
        'chief_complaint' => $this->form->chief_complaint,
        'encounter_date' => $this->form->encounter_date,
        'notes' => $this->form->notes,
    ]);

    // Log encounter update activity
    $this->encounter->recordActivity('updated', 'Medical encounter was updated');
    
    session()->flash('message', 'Encounter updated successfully.');
    $this->redirect(route('encounters.show', $this->encounter));
};

$patients = function () {
    return Patient::orderBy('first_name')->orderBy('last_name')->get();
};

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $encounter ? 'Edit Encounter' : 'Create New Encounter' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $encounter ? 'Update encounter details' : 'Add a new patient encounter' }}
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('encounters.index') }}" 
               class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                </svg>
                Back to Encounters
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Patient Selection -->
            <div>
                <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Patient <span class="text-red-500">*</span>
                </label>
                <select wire:model="form.patient_id" 
                        id="patient_id"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        {{ $encounter ? 'disabled' : '' }}>
                    <option value="">Select a patient...</option>
                    @foreach($this->patients() as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }} 
                            (ID: {{ $patient->patient_id }})
                        </option>
                    @endforeach
                </select>
                @error('form.patient_id') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Encounter Date -->
            <div>
                <label for="encounter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Encounter Date <span class="text-red-500">*</span>
                </label>
                <input type="date" 
                       wire:model="form.encounter_date" 
                       id="encounter_date"
                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                @error('form.encounter_date') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Chief Complaint -->
            <div>
                <label for="chief_complaint" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Chief Complaint <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="form.chief_complaint" 
                          id="chief_complaint" 
                          rows="3"
                          placeholder="Enter the patient's chief complaint..."
                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('form.chief_complaint') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes
                </label>
                <textarea wire:model="form.notes" 
                          id="notes" 
                          rows="4"
                          placeholder="Additional notes about the encounter..."
                          class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('form.notes') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('encounters.index') }}" 
                   class="btn btn-secondary">
                    Cancel
                </a>
                <button type="submit" 
                        class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-2">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    {{ $encounter ? 'Update Encounter' : 'Create Encounter' }}
                </button>
            </div>
        </form>
    </div>
</div>
