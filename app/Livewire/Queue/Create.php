<?php

namespace App\Livewire\Queue;

use App\Models\Patient;
use App\Models\Department;
use Livewire\Component;

class Create extends Component
{
    public Patient $patient;
    public $department_id = '';
    public $priority = 'normal';
    public $notes = '';

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function render()
    {
        return view('livewire.queue.create', [
            'departments' => Department::active()->get()
        ]);
    }

    public function addToQueue()
    {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        // Add queue creation logic here

        $this->dispatch('close-modal', 'add-to-queue');
    }
}
