<?php

namespace App\Http\Controllers;

use App\Classes\PrescriptionItem;
use App\Models\Encounter;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\PatientAddress;
use App\Services\PrescriptionInvoice;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class Prescription extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id)
    {
        $encounter = Encounter::findOrFail($id);

        $client = new Party([
            'owner' => config('settings.business_owner'),
            'prc' => config('settings.prc'),
            'ptr' => config('settings.ptr'),
            's2' => config('settings.s2')
        ]);

        $patient_address = PatientAddress::with('address')->where('patient_id', $encounter->patient->id)->first();
        $patientObj = Patient::findOrFail($encounter->patient_id);
        $patient = new Party([
            'id' => $patientObj->id,
            'name' => $patientObj->full_name,
            'age' => $patientObj->age,
            'address' => $patient_address ? $patient_address->address->line_1 . ',' . $patient_address->address->district . ', ' . $patient_address->address->city->name : '--',
            'birthdate' => $patientObj->date_of_birth,
            'gender' => $patientObj->gender
        ]);
        // dd($patient->name);
        $medications = Medication::where('encounter_id', $encounter->id)->get();
        foreach ($medications as $medication) {
            $prescriptions[] = (new PrescriptionItem())
                ->title($medication->medication_name)
                ->description($medication->frequency)
                ->quantity($medication->dosage);
        }

        $logo = public_path('storage/' . config('settings.logo'));

        $pdf = Pdf::loadView('prescription', [
            'logo' => $logo,
            'patient' => $patient,
            'prescriptions' => $prescriptions,
            'client' => $client
        ]);
        $pdf->setOption(['dpi' => 150, 'defaultFont' => 'monospace']);
        $pdf->setPaper('a5', 'portrait');
        return $pdf->stream();
    }
}
