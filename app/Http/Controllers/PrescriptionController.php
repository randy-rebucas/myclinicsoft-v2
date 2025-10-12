<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Services\PrescriptionPdfGenerator;
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
     * Print prescription - returns PDF stream for immediate printing
     */
    public function print(Prescription $prescription)
    {
        try {
            // Generate PDF using our custom generator
            $pdfGenerator = new PrescriptionPdfGenerator($prescription);
            $pdfContent = $pdfGenerator->generate();
            $prescriptionNumber = $pdfGenerator->getPrescriptionNumber();
            $filename = 'prescription_' . $prescriptionNumber . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate prescription PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download prescription PDF - forces download
     */
    public function download(Prescription $prescription)
    {
        try {
            $pdfGenerator = new PrescriptionPdfGenerator($prescription);
            $pdfContent = $pdfGenerator->generate();
            $prescriptionNumber = $pdfGenerator->getPrescriptionNumber();
            $filename = 'prescription_' . $prescriptionNumber . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download prescription PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get prescription PDF data as JSON (for API usage)
     */
    public function getPdfData(Prescription $prescription): JsonResponse
    {
        try {
            $pdfGenerator = new PrescriptionPdfGenerator($prescription);
            $pdfContent = $pdfGenerator->generate();
            $prescriptionNumber = $pdfGenerator->getPrescriptionNumber();

            return response()->json([
                'success' => true,
                'message' => 'Prescription PDF generated successfully',
                'data' => [
                    'prescription' => $prescription->load(['patient', 'doctor', 'encounter']),
                    'prescription_number' => $prescriptionNumber,
                    'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdfContent),
                    'print_url' => route('prescriptions.print', ['prescription' => $prescription->id]),
                    'download_url' => route('prescriptions.download', ['prescription' => $prescription->id])
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
