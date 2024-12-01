<?php

use Livewire\Volt\Component;
use App\Models\Department;
use App\Livewire\Forms\QueueForm;
use function Livewire\Volt\{state, form, mount, computed, on};

form(QueueForm::class);

state('departments');

mount(function () {
    $this->departments = Department::all();
});

on(['set-patient' => function ($patientId) {
    $patient = Patient::find($patientId);
    $this->form->department_id = 1;
    $this->form->patient_id = $patient->id;
    $this->form->priority = 'normal';
}]);

$departments = computed(function () {
    return Department::active()->get();
});

$createQueue = function () {
    $this->form->store();
    $this->dispatch('create-queue');
};
?>

<div>
    <form wire:submit="createQueue" class="p-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Add to Queue
        </h2>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                <select wire:model.live="form.department_id" name="department_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="">Select Department</option>
                    @foreach ($this->departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('form.department_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                <select wire:model.live="form.priority" name="priority"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <textarea wire:model.live="form.notes" name="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"></textarea>
            </div>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Add to que') }}
            </x-primary-button>
        </div>
    </form>
</div>
