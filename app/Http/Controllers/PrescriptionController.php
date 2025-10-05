<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Services\PrescriptionInvoice;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PrescriptionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Print prescription
     */
    public function print(Prescription $prescription): JsonResponse
    {
        try {
            // Create prescription invoice
            $invoice = new PrescriptionInvoice('Prescription');
            
            // Add prescription to invoice
            $prescriptionItem = $invoice->makeItem($prescription->medication_name)
                ->pricePerUnit($prescription->dosage)
                ->quantity($prescription->quantity)
                ->subTotalPrice($prescription->quantity * 1); // Assuming price per unit is 1

            $invoice->addPrescription($prescriptionItem);

            // Generate PDF
            $pdf = $invoice->stream();

            return response()->json([
                'success' => true,
                'message' => 'Prescription PDF generated successfully',
                'data' => [
                    'prescription' => $prescription->load(['patient', 'doctor', 'encounter']),
                    'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdf)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate prescription PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark prescription as ready
     */
    public function markReady(Prescription $prescription): JsonResponse
    {
        try {
            $prescription->update(['status' => 'ready']);

            // Send notification to patient
            if ($prescription->patient->user) {
                $this->notificationService->sendPrescriptionReady(
                    $prescription->patient->user,
                    $prescription
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Prescription marked as ready',
                'data' => $prescription->load(['patient', 'doctor', 'encounter'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark prescription as ready',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
