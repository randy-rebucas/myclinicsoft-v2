<?php

namespace App\Http\Controllers;

use App\Classes\PrescriptionItem;
use App\Models\Encounter;
use App\Models\Medication;
use App\Models\Patient;
use App\Services\PrescriptionInvoice;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class Prescription extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id)
    {
        $medication = Medication::findOrFail($id);

        $doctor = Auth::user()->doctor;

        $client = new Party([
            'owner' => $doctor->full_name,
            'prc' => $doctor->meta['PRC'] ?? null,
            'ptr' => $doctor->meta['PTR'] ?? null,
            's2' => $doctor->meta['S2'] ?? null
        ]);

        $patient = new Party([
            'id' => $medication->patient_id,
            'name' => $medication->patient->full_name,
            'age' => $medication->patient->age,
            'address' => $medication->patient->address ? $medication->patient->address->address_line_1 . ',' . $medication->patient->address->city . ', ' . $medication->patient->address->state : null,
            'birthdate' => $medication->patient->date_of_birth,
            'gender' => $medication->patient->gender
        ]);

        $prescriptions = [];
        foreach ($medication->prescription_items as $prescription) {
            $prescriptions[] = (new PrescriptionItem())
                ->title($prescription['medication_name'])
                ->description($prescription['frequency'])
                ->quantity($prescription['dosage']);
        }

        $logo = public_path('storage/' . config('settings.logo'));

        $pdf = Pdf::loadView('prescription', [
            'logo' => $logo,
            'patient' => $patient,
            'prescriptions' => $prescriptions,
            'client' => $client,
            'follow_up' => $request->get('follow-up') ?? null
        ]);
        $pdf->setOption(['dpi' => 150, 'defaultFont' => 'monospace']);
        $pdf->setPaper('a5', 'portrait');
        return $pdf->stream();
    }
}
