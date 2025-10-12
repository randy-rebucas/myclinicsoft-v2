<?php

namespace App\Services;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PrescriptionPdfGenerator
{
    protected $prescription;
    protected $doctor;
    protected $patient;

    public function __construct(Prescription $prescription)
    {
        $this->prescription = $prescription->load(['patient.address', 'doctor.user', 'encounter']);
        $this->doctor = $this->prescription->doctor;
        $this->patient = $this->prescription->patient;
    }

    /**
     * Generate prescription PDF
     */
    public function generate(): string
    {
        $data = $this->prepareData();
        
        $pdf = Pdf::loadView('pdfs.prescription', $data)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        return $pdf->output();
    }

    /**
     * Generate and save prescription PDF
     */
    public function generateAndSave(string $filename = null): string
    {
        $filename = $filename ?: 'prescription_' . $this->prescription->id . '_' . time() . '.pdf';
        $pdfContent = $this->generate();
        
        Storage::disk('public')->put('prescriptions/' . $filename, $pdfContent);
        
        return $filename;
    }

    /**
     * Get PDF as base64 string
     */
    public function getBase64(): string
    {
        return base64_encode($this->generate());
    }

    /**
     * Prepare data for PDF template
     */
    protected function prepareData(): array
    {
        return [
            'prescription' => $this->prescription,
            'doctor' => $this->doctor,
            'patient' => $this->patient,
            'clinic' => $this->getClinicInfo(),
            'logo' => $this->getLogoPath(),
            'currentDate' => now()->format('m/d/Y'),
            'prescriptionDate' => $this->prescription->start_date->format('m/d/Y'),
            'prescriptionNumber' => $this->getPrescriptionNumber(),
            'doctorInfo' => $this->getDoctorInfo(),
            'patientInfo' => $this->getPatientInfo(),
            'medicationInfo' => $this->getMedicationInfo(),
            'instructions' => $this->getInstructions(),
        ];
    }

    /**
     * Get clinic information
     */
    protected function getClinicInfo(): array
    {
        // Try to get clinic info from doctor's clinic or use default
        $clinic = $this->doctor->clinic ?? null;
        
        return [
            'name' => $clinic->name ?? config('app.name', 'Medical Clinic'),
            'location' => $clinic->location ?? 'City, Country',
            'address' => $clinic->address ?? '123 Medical Street, Healthcare City',
            'phone' => $clinic->phone_number ?? 'N/A',
            'mobile' => $clinic->mobile_number ?? 'N/A',
            'email' => $clinic->email ?? 'info@medicalclinic.com',
            'website' => $clinic->website ?? 'www.medicalclinic.com',
        ];
    }

    /**
     * Get logo path
     */
    protected function getLogoPath(): ?string
    {
        $logoPath = public_path('storage/' . config('settings.logo', 'logo.png'));
        return file_exists($logoPath) ? $logoPath : null;
    }

    /**
     * Get doctor information
     */
    protected function getDoctorInfo(): array
    {
        return [
            'name' => $this->doctor->user->name,
            'specialization' => $this->doctor->specialization ?? 'General Medicine',
            'prc' => $this->doctor->meta['PRC'] ?? null,
            'ptr' => $this->doctor->meta['PTR'] ?? null,
            's2' => $this->doctor->meta['S2'] ?? null,
            'license' => $this->doctor->meta['license'] ?? null,
        ];
    }

    /**
     * Get patient information
     */
    protected function getPatientInfo(): array
    {
        return [
            'name' => $this->patient->first_name . ' ' . $this->patient->last_name,
            'patient_id' => $this->patient->patient_id ?? 'N/A',
            'age' => $this->patient->age ?? 'N/A',
            'gender' => ucfirst($this->patient->gender ?? 'N/A'),
            'birthdate' => $this->patient->date_of_birth ? $this->patient->date_of_birth->format('F d, Y') : 'N/A',
            'birth_year' => $this->patient->date_of_birth ? $this->patient->date_of_birth->format('Y') : 'N/A',
            'address' => $this->getPatientAddress(),
            'phone' => $this->patient->phone_number ?? 'N/A',
            'email' => $this->patient->email ?? 'N/A',
        ];
    }

    /**
     * Get patient address
     */
    protected function getPatientAddress(): string
    {
        if (!$this->patient->address) {
            return 'No address provided';
        }

        $address = $this->patient->address;
        $parts = array_filter([
            $address->address_line_1 ?? '',
            $address->address_line_2 ?? '',
            $address->city ?? '',
            $address->state ?? '',
            $address->postal_code ?? '',
            $address->country ?? '',
        ]);

        return implode(', ', $parts) ?: 'No address provided';
    }

    /**
     * Get medication information
     */
    protected function getMedicationInfo(): array
    {
        return [
            'name' => $this->prescription->medication_name ?? 'N/A',
            'dosage' => $this->prescription->dosage ?? 'N/A',
            'frequency' => $this->prescription->frequency ?? 'N/A',
            'quantity' => $this->prescription->quantity ?? 'N/A',
            'refills' => $this->prescription->refills ?? 0,
            'start_date' => $this->prescription->start_date ? $this->prescription->start_date->format('F d, Y') : 'N/A',
            'end_date' => $this->prescription->end_date ? $this->prescription->end_date->format('F d, Y') : 'N/A',
            'status' => ucfirst($this->prescription->status ?? 'Active'),
        ];
    }

    /**
     * Get prescription instructions
     */
    protected function getInstructions(): string
    {
        if ($this->prescription->instructions) {
            return $this->prescription->instructions;
        }

        return 'Take as directed.';
    }

    /**
     * Get prescription signature line
     */
    public function getSignatureLine(): string
    {
        return "Dr. " . $this->doctor->user->name;
    }

    /**
     * Get prescription number
     */
    public function getPrescriptionNumber(): string
    {
        return 'RX-' . str_pad($this->prescription->id, 6, '0', STR_PAD_LEFT);
    }
}
