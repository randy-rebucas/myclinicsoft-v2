<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Services\QueueService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    protected $queueService;
    protected $notificationService;

    public function __construct(QueueService $queueService, NotificationService $notificationService)
    {
        $this->queueService = $queueService;
        $this->notificationService = $notificationService;
    }

    /**
     * Call next patient in queue
     */
    public function call(Queue $queue): JsonResponse
    {
        try {
            $updatedQueue = $this->queueService->callNext($queue->clinic, $queue->doctor);
            
            if ($updatedQueue) {
                // Send notification to patient
                if ($updatedQueue->patient->user) {
                    $this->notificationService->sendQueueUpdate(
                        $updatedQueue->patient->user,
                        $updatedQueue,
                        'called'
                    );
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Patient called successfully',
                    'data' => $updatedQueue->load(['patient', 'doctor', 'clinic'])
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No patients in queue'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to call patient',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete queue entry
     */
    public function complete(Queue $queue): JsonResponse
    {
        try {
            $updatedQueue = $this->queueService->complete($queue);

            // Send notification to patient
            if ($updatedQueue->patient->user) {
                $this->notificationService->sendQueueUpdate(
                    $updatedQueue->patient->user,
                    $updatedQueue,
                    'completed'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Queue entry completed successfully',
                'data' => $updatedQueue->load(['patient', 'doctor', 'clinic'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete queue entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel queue entry
     */
    public function cancel(Request $request, Queue $queue): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $updatedQueue = $this->queueService->cancel($queue, $request->reason);

            // Send notification to patient
            if ($updatedQueue->patient->user) {
                $this->notificationService->sendQueueUpdate(
                    $updatedQueue->patient->user,
                    $updatedQueue,
                    'cancelled'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Queue entry cancelled successfully',
                'data' => $updatedQueue->load(['patient', 'doctor', 'clinic'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel queue entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get queue status
     */
    public function status(Queue $queue): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'queue' => $queue->load(['patient', 'doctor', 'clinic']),
                'status' => $queue->status,
                'position' => $this->getQueuePosition($queue)
            ]
        ]);
    }

    /**
     * Get queue position
     */
    private function getQueuePosition(Queue $queue): int
    {
        return Queue::where('clinic_id', $queue->clinic_id)
            ->where('status', 'waiting')
            ->where('created_at', '<', $queue->created_at)
            ->count() + 1;
    }
}
