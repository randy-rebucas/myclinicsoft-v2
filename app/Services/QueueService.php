<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Patient;
use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Add a patient to the queue.
     *
     * @param Patient $patient
     * @param Clinic $clinic
     * @param Doctor|null $doctor
     * @param string $priority
     * @param string|null $notes
     * @return Queue
     */
    public function addToQueue(Patient $patient, Clinic $clinic, ?Doctor $doctor = null, string $priority = 'normal', ?string $notes = null): Queue
    {
        $queueNumber = $this->generateQueueNumber($clinic);
        
        return Queue::create([
            'patient_id' => $patient->id,
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor?->id,
            'queue_number' => $queueNumber,
            'status' => 'waiting',
            'priority' => $priority,
            'notes' => $notes,
        ]);
    }

    /**
     * Call the next patient in queue.
     *
     * @param Clinic $clinic
     * @param Doctor|null $doctor
     * @return Queue|null
     */
    public function callNext(Clinic $clinic, ?Doctor $doctor = null): ?Queue
    {
        $query = Queue::where('clinic_id', $clinic->id)
            ->where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        if ($doctor) {
            $query->where('doctor_id', $doctor->id);
        }

        $queue = $query->first();

        if ($queue) {
            $queue->update([
                'status' => 'called',
                'called_at' => now(),
            ]);
        }

        return $queue;
    }

    /**
     * Complete a queue entry.
     *
     * @param Queue $queue
     * @return Queue
     */
    public function complete(Queue $queue): Queue
    {
        $queue->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $queue;
    }

    /**
     * Cancel a queue entry.
     *
     * @param Queue $queue
     * @param string|null $reason
     * @return Queue
     */
    public function cancel(Queue $queue, ?string $reason = null): Queue
    {
        $queue->update([
            'status' => 'cancelled',
            'notes' => $reason ? ($queue->notes . "\nCancelled: " . $reason) : $queue->notes,
        ]);

        return $queue;
    }

    /**
     * Get current queue status for a clinic.
     *
     * @param Clinic $clinic
     * @param Doctor|null $doctor
     * @return array
     */
    public function getQueueStatus(Clinic $clinic, ?Doctor $doctor = null): array
    {
        $query = Queue::where('clinic_id', $clinic->id);

        if ($doctor) {
            $query->where('doctor_id', $doctor->id);
        }

        return [
            'waiting' => $query->where('status', 'waiting')->count(),
            'called' => $query->where('status', 'called')->count(),
            'in_progress' => $query->where('status', 'in_progress')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Generate a unique queue number for the clinic.
     *
     * @param Clinic $clinic
     * @return string
     */
    private function generateQueueNumber(Clinic $clinic): string
    {
        $today = now()->format('Y-m-d');
        $lastQueue = Queue::where('clinic_id', $clinic->id)
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastQueue ? (int) substr($lastQueue->queue_number, -3) + 1 : 1;

        return $clinic->id . '-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
