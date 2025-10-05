<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $notificationService;

    public function __construct(AppointmentService $appointmentService, NotificationService $notificationService)
    {
        $this->appointmentService = $appointmentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Confirm appointment
     */
    public function confirm(Appointment $appointment): JsonResponse
    {
        try {
            $updatedAppointment = $this->appointmentService->confirmAppointment($appointment);

            // Send confirmation notification
            if ($appointment->patient->user) {
                $this->notificationService->sendAppointmentReminder(
                    $appointment->patient->user,
                    $appointment
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Appointment confirmed successfully',
                'data' => $updatedAppointment->load(['patient', 'doctor', 'clinic'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            $updatedAppointment = $this->appointmentService->cancelAppointment($appointment, $request->reason);

            // Send cancellation notification
            if ($appointment->patient->user) {
                $this->notificationService->sendAppointmentCancellation(
                    $appointment->patient->user,
                    $appointment,
                    $request->reason
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully',
                'data' => $updatedAppointment->load(['patient', 'doctor', 'clinic'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete appointment
     */
    public function complete(Appointment $appointment): JsonResponse
    {
        try {
            $updatedAppointment = $this->appointmentService->completeAppointment($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment completed successfully',
                'data' => $updatedAppointment->load(['patient', 'doctor', 'clinic'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
