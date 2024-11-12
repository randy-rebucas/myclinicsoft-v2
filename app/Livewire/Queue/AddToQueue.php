<?php

namespace App\Livewire\Queue;

use App\Models\Department;
use Livewire\Component;

class AddToQueue extends Component
{
    public $showModal = false;
    public $department_id = '';
    public $priority = 'normal';
    public $notes = '';

    public function render()
    {
        return view('livewire.queue.add-to-queue', [
            'departments' => Department::active()->get()
        ]);
    }

    public function addToQueue()
    {
        $this->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        // Add queue creation logic here

        $this->showModal = false;
    }
}
