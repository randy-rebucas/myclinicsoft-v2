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
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Setting;
use Carbon\Carbon;

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
        try {
            // Get the first medication from the collection
            $medication = $models->first();

            if (!$medication) {
                return Action::danger('No medication selected.');
            }

            // Get the authenticated user and doctor
            $user = Auth::user();
            if (!$user) {
                return Action::danger('User not authenticated.');
            }

            $doctor = $user->doctor;
            if (!$doctor) {
                return Action::danger('Doctor profile not found.');
            }

            // Get patient information with address relationship
            $patient = $medication->patient()->with('address')->first();
            if (!$patient) {
                return Action::danger('Patient not found for this medication.');
            }

            // Get prescription items and validate
            $items = $medication->prescription_items ?? [];
            if (empty($items)) {
                return Action::danger('No prescription items found for this medication.');
            }

            // Ensure default clinic settings exist
            $this->ensureDefaultSettings();

            // Prepare data for the template
            $templateData = $this->prepareTemplateData($medication, $patient, $doctor, $items);

            // Load the PDF view with all necessary data
            $pdf = PDF::loadView('pdfs.prescription', $templateData)->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultCharset' => 'utf-8',
                'dpi' => 150,
                'defaultPaperSize' => 'a4',
                'isJavascriptEnabled' => false,
                'isCssFloatEnabled' => true,
                'isCssPositionEnabled' => true,
            ]);

            // Set paper size to A4 for perfect prescription size
            $pdf->setPaper('a4', 'portrait');

            // Generate filename with timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = sprintf(
                'prescription_%s_patient_%s_%s.pdf',
                $medication->id,
                $patient->id,
                $timestamp
            );

            // Clean filename for safe download
            $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

            // Return the PDF as a download
            return Action::download($pdf->output(), $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Prescription PDF generation failed: ' . $e->getMessage(), [
                'medication_id' => $medication->id ?? 'unknown',
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Action::danger('Failed to generate prescription PDF. Please try again or contact support.');
        }
    }

    /**
     * Prepare template data with proper validation and fallbacks
     *
     * @param \App\Models\Medication $medication
     * @param \App\Models\Patient $patient
     * @param \App\Models\Doctor $doctor
     * @param array $items
     * @return array
     */
    private function prepareTemplateData($medication, $patient, $doctor, $items)
    {
        // Validate and clean prescription items
        $validatedItems = $this->validatePrescriptionItems($items);

        // Prepare patient data with fallbacks
        $patientData = [
            'id' => $patient->id ?? 'N/A',
            'full_name' => $patient->full_name ?? 'N/A',
            'date_of_birth' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->format('M d, Y') : 'N/A',
            'age' => $patient->age ?? 'N/A',
            'gender' => ucfirst($patient->gender ?? 'N/A'),
            'address' => $this->formatPatientAddress($patient->address)
        ];

        // Prepare doctor data with fallbacks
        $doctorData = [
            'full_name' => $doctor->full_name ?? 'Dr. Practitioner',
            'meta' => $doctor->meta ?? []
        ];

        // Prepare medication data with fallbacks
        $medicationData = [
            'id' => $medication->id ?? 'N/A',
            'created_at' => $medication->created_at ? Carbon::parse($medication->created_at) : null,
            'notes' => $medication->notes ?? null,
            'follow_up_date' => $medication->follow_up_date ? Carbon::parse($medication->follow_up_date) : null
        ];

        return [
            'medication' => $medicationData,
            'patient' => $patientData,
            'doctor' => $doctorData,
            'items' => $validatedItems,
        ];
    }

    /**
     * Validate and clean prescription items
     *
     * @param array $items
     * @return array
     */
    private function validatePrescriptionItems($items)
    {
        $validatedItems = [];

        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['fields'])) {
                continue;
            }

            $fields = $item['fields'];

            // Validate required fields and provide fallbacks
            $validatedItem = [
                'fields' => [
                    'medication_name' => $fields['medication_name'] ?? 'N/A',
                    'generic_name' => $fields['generic_name'] ?? null,
                    'dosage' => $fields['dosage'] ?? 'N/A',
                    'frequency' => $fields['frequency'] ?? 'N/A',
                    'duration' => $fields['duration'] ?? null,
                    'special_instructions' => $fields['special_instructions'] ?? null,
                    'refills' => isset($fields['refills']) ? (int) $fields['refills'] : 0,
                ]
            ];

            $validatedItems[] = $validatedItem;
        }

        return $validatedItems;
    }

    /**
     * Format patient address for display
     *
     * @param \App\Models\Address|null $address
     * @return string
     */
    private function formatPatientAddress($address)
    {
        if (!$address) {
            return 'N/A';
        }

        $addressParts = [];

        if (!empty($address->address_line_1)) {
            $addressParts[] = $address->address_line_1;
        }

        if (!empty($address->address_line_2)) {
            $addressParts[] = $address->address_line_2;
        }

        if (!empty($address->city)) {
            $addressParts[] = $address->city;
        }

        if (!empty($address->state)) {
            $addressParts[] = $address->state;
        }

        if (!empty($address->postal_code)) {
            $addressParts[] = $address->postal_code;
        }

        return empty($addressParts) ? 'N/A' : implode(', ', $addressParts);
    }

    /**
     * Ensure default clinic settings exist
     */
    private function ensureDefaultSettings()
    {
        $defaultSettings = [
            'clinic_name' => 'Medical Clinic',
            'clinic_address' => '123 Medical Center Dr.',
            'clinic_city' => 'Medical City',
            'clinic_state' => 'MC',
            'clinic_zip' => '12345',
            'clinic_phone' => '(555) 123-4567',
            'clinic_email' => 'info@medicalclinic.com',
        ];

        foreach ($defaultSettings as $key => $defaultValue) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                Setting::create([
                    'key' => $key,
                    'value' => $defaultValue
                ]);

                // Update config immediately
                config(['settings.' . $key => $defaultValue]);
            }
        }
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

    /**
     * Get the action's display name.
     *
     * @return string
     */
    public function displayName()
    {
        return 'Print Prescription PDF';
    }


}
