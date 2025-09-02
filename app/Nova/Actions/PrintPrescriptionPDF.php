<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Nova\Actions\ActionResponse;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintPrescriptionPDF extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $medication = $models->first();

        // Get the authenticated doctor
        $doctor = Auth::user()->doctor;

        // Get patient information with address relationship
        $patient = $medication->patient()->with('address')->first();

        // Get prescription items
        $items = $medication->prescription_items ?? [];

        // Generate QR code for patient ID
        $qrCode = QrCode::size(80)->generate($patient->id ?? 'N/A');

        // Load the PDF view with all necessary data
        $pdf = PDF::loadView('pdfs.prescription', [
            'medication' => $medication,
            'patient' => $patient,
            'doctor' => $doctor,
            'items' => $items,
            'qrCode' => $qrCode,
        ])->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultCharset' => 'utf-8',
            'dpi' => 150,
            'defaultPaperSize' => 'a4',
        ]);

        // Set paper size to A4 for perfect prescription size
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'prescription-' . $medication->id . '-' . ($patient->id ?? 'unknown') . '.pdf';

        // Return the PDF as a download
        return Action::download($pdf->output(), $filename);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return 'Print Prescription PDF';
    }
}
