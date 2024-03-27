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
            'name' => $patientObj->full_name,
            'age' => $patientObj->age,
            'address' => $patient_address->address->line_1 . ',' . $patient_address->address->district . ', ' . $patient_address->address->city->name,
            'birthdate' => $patientObj->date_of_birth,
            'gender' => $patientObj->gender
        ]);

        $medications = Medication::where('encounter_id', $encounter->id)->get();
        foreach ($medications as $medication) {
            $prescriptions[] = (new PrescriptionItem())
                ->title($medication->item->name)
                ->pricePerUnit($medication->unit_price)
                ->subTotalPrice($medication->sub_total)
                ->quantity($medication->quantity);
        }

        // $notes = [123];
        // $notes = implode("<br>", $notes);
        
        $invoice = PrescriptionInvoice::make('receipt')->template('prescription')
            ->series('BIG')
            // ability to include translated invoice status
            // in case it was paid
            ->status(__('invoices::invoice.paid'))
            ->sequence(667)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            // ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($client->name . ' ' . $encounter->patient->name)
            // ->addItems($items)
            // ->notes($notes)

            ->patient($patient)
            ->addPrescriptions($prescriptions)
            ->patientId(123)

            ->logo(public_path('storage/' . config('settings.logo')))
            // You can additionally save generated invoice to configured disk
            ->save('public');
        
        $link = $invoice->url();
        // Then send email to party with link
        
        // And return invoice itself to browser or have a different view
        return $invoice->stream();
    }
}
