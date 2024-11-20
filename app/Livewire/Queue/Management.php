<?php

namespace App\Livewire\Queue;

use App\Models\Queue;
use App\Models\Department;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class Management extends Component
{
    use WithPagination;

    #[Rule('required')]
    public $department_id;

    #[Rule('required')]
    public $priority = 'normal';

    public $notes;
    public $selectedQueue;
    public $filter = 'waiting';

    public function addToQueue($patientId)
    {
        $this->validate();

        $lastQueue = Queue::where('department_id', $this->department_id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $queueNumber = $lastQueue
            ? sprintf('%03d', intval(substr($lastQueue->queue_number, -3)) + 1)
            : '001';

        $departmentCode = Department::find($this->department_id)->code;
        $fullQueueNumber = $departmentCode . date('ymd') . $queueNumber;

        Queue::create([
            'patient_id' => $patientId,
            'department_id' => $this->department_id,
            'queue_number' => $fullQueueNumber,
            'priority' => $this->priority,
            'notes' => $this->notes,
        ]);

        $this->reset(['priority', 'notes']);
        $this->dispatch('queue-updated');
    }

    public function callNext($queueId)
    {
        $queue = Queue::find($queueId);
        $queue->update([
            'status' => 'in_progress',
            'called_at' => now(),
        ]);

        $this->dispatch('queue-updated');
    }

    public function complete($queueId)
    {
        $queue = Queue::find($queueId);
        $queue->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->dispatch('queue-updated');
    }

    public function cancel($queueId)
    {
        $queue = Queue::find($queueId);
        $queue->update(['status' => 'cancelled']);

        $this->dispatch('queue-updated');
    }

    public function render()
    {
        $departments = Department::all();

        $queues = Queue::with(['patient', 'department'])
            ->when($this->filter !== 'all', function ($query) {
                $query->where('status', $this->filter);
            })
            ->when($this->department_id, function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.queue.management', [
            'departments' => $departments,
            'queues' => $queues,
        ]);
    }
}
