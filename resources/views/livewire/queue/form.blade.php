<?php

use Livewire\Volt\Component;
use App\Models\Department;
use App\Models\Patient;
use App\Models\ClinicDoctor;
use App\Livewire\Forms\QueueForm;
use function Livewire\Volt\{state, form, mount, computed, on};

form(QueueForm::class);

state([
    'departments',
    'patients' => [],
    'clinics' => []
]);

mount(function () {
    $this->departments = Department::all();
    $this->patients = Patient::with('user')->get();
    $this->clinics = ClinicDoctor::with('clinic')
        ->where('doctor_id', auth()->user()->doctor->id)
        ->get();
});

on(['set-patient' => function ($patientId) {
    $patient = Patient::find($patientId);
    $this->form->patient_id = $patient->id;
    $this->form->priority = 'normal';
}]);

$departments = computed(function () {
    return Department::active()->get();
});

$createQueue = function () {
    try {
        $this->form->store();
        session()->flash('success', 'Patient has been successfully added to the queue!');
        $this->dispatch('create-queue');
        $this->form->reset();
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to add patient to queue: ' . $e->getMessage());
    }
};
?>

<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 m-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 m-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="createQueue" class="p-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Add to Queue
        </h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
                <select wire:model.live="form.patient_id" name="patient_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Select a patient</option>
                    @foreach ($this->patients as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }} 
                            @if($patient->user && $patient->user->email)
                                ({{ $patient->user->email }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('form.patient_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Clinic</label>
                <select wire:model.live="form.clinic_id" name="clinic_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Select a clinic</option>
                    @foreach ($this->clinics as $clinicDoctor)
                        <option value="{{ $clinicDoctor->clinic->id }}">
                            {{ $clinicDoctor->clinic->name }}
                        </option>
                    @endforeach
                </select>
                @error('form.clinic_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                <select wire:model.live="form.priority" name="priority" required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                    <option value="emergency">Emergency</option>
                </select>
                @error('form.priority')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <textarea wire:model.live="form.notes" name="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                    placeholder="Optional notes about the patient's condition or special requirements..."></textarea>
                @error('form.notes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Add to Queue') }}
            </x-primary-button>
        </div>
    </form>
</div>
