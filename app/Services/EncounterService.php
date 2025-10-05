<?php

namespace App\Services;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Medication;
use App\Models\Prescription;
use Carbon\Carbon;

class EncounterService
{
    /**
     * Create a new encounter.
     *
     * @param Patient $patient
     * @param Doctor $doctor
     * @param array $encounterData
     * @return Encounter
     */
    public function createEncounter(Patient $patient, Doctor $doctor, array $encounterData): Encounter
    {
        return Encounter::create(array_merge($encounterData, [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'encounter_date' => $encounterData['encounter_date'] ?? now()->toDateString(),
            'encounter_time' => $encounterData['encounter_time'] ?? now()->toTimeString(),
            'status' => $encounterData['status'] ?? 'scheduled',
        ]));
    }

    /**
     * Start an encounter.
     *
     * @param Encounter $encounter
     * @return Encounter
     */
    public function startEncounter(Encounter $encounter): Encounter
    {
        $encounter->update([
            'status' => 'in_progress',
            'encounter_time' => now()->toTimeString(),
        ]);
        return $encounter;
    }

    /**
     * Complete an encounter.
     *
     * @param Encounter $encounter
     * @param array $completionData
     * @return Encounter
     */
    public function completeEncounter(Encounter $encounter, array $completionData = []): Encounter
    {
        $encounter->update(array_merge([
            'status' => 'completed',
        ], $completionData));
        return $encounter;
    }

    /**
     * Add medication to encounter.
     *
     * @param Encounter $encounter
     * @param array $medicationData
     * @return Medication
     */
    public function addMedication(Encounter $encounter, array $medicationData): Medication
    {
        return Medication::create(array_merge($medicationData, [
            'patient_id' => $encounter->patient_id,
            'encounter_id' => $encounter->id,
        ]));
    }

    /**
     * Create prescription from encounter.
     *
     * @param Encounter $encounter
     * @param array $prescriptionData
     * @return Prescription
     */
    public function createPrescription(Encounter $encounter, array $prescriptionData): Prescription
    {
        return Prescription::create(array_merge($prescriptionData, [
            'patient_id' => $encounter->patient_id,
            'doctor_id' => $encounter->doctor_id,
            'encounter_id' => $encounter->id,
            'status' => 'active',
        ]));
    }

    /**
     * Get encounter summary.
     *
     * @param Encounter $encounter
     * @return array
     */
    public function getEncounterSummary(Encounter $encounter): array
    {
        return [
            'encounter' => $encounter,
            'patient' => $encounter->patient,
            'doctor' => $encounter->doctor,
            'medications' => $encounter->medications,
            'prescriptions' => $encounter->prescriptions,
            'duration' => $this->calculateDuration($encounter),
        ];
    }

    /**
     * Get patient's encounter history.
     *
     * @param Patient $patient
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPatientEncounterHistory(Patient $patient, int $limit = 10)
    {
        return Encounter::where('patient_id', $patient->id)
            ->with(['doctor', 'medications', 'prescriptions'])
            ->orderBy('encounter_date', 'desc')
            ->orderBy('encounter_time', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get doctor's encounters for a date.
     *
     * @param Doctor $doctor
     * @param Carbon $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDoctorEncountersForDate(Doctor $doctor, Carbon $date)
    {
        return Encounter::where('doctor_id', $doctor->id)
            ->whereDate('encounter_date', $date)
            ->with(['patient'])
            ->orderBy('encounter_time')
            ->get();
    }

    /**
     * Calculate encounter duration.
     *
     * @param Encounter $encounter
     * @return int|null Duration in minutes
     */
    private function calculateDuration(Encounter $encounter): ?int
    {
        if (!$encounter->encounter_time || !$encounter->duration) {
            return null;
        }

        return $encounter->duration;
    }

    /**
     * Get encounters by status.
     *
     * @param string $status
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEncountersByStatus(string $status, int $limit = 50)
    {
        return Encounter::where('status', $status)
            ->with(['patient', 'doctor'])
            ->orderBy('encounter_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update encounter diagnosis and treatment plan.
     *
     * @param Encounter $encounter
     * @param string $diagnosis
     * @param string $treatmentPlan
     * @param Carbon|null $followUpDate
     * @return Encounter
     */
    public function updateDiagnosisAndTreatment(
        Encounter $encounter,
        string $diagnosis,
        string $treatmentPlan,
        ?Carbon $followUpDate = null
    ): Encounter {
        $encounter->update([
            'diagnosis' => $diagnosis,
            'treatment_plan' => $treatmentPlan,
            'follow_up_date' => $followUpDate,
        ]);
        return $encounter;
    }

    /**
     * Get encounter count for a specific doctor by month and year.
     *
     * @param int $doctorId
     * @param int $month
     * @param int $year
     * @return int
     */
    public function getEncounterCountByMonthAndYear(int $doctorId, int $month, int $year): int
    {
        return Encounter::where('doctor_id', $doctorId)
            ->whereMonth('encounter_date', $month)
            ->whereYear('encounter_date', $year)
            ->count();
    }

    /**
     * Get encounter count for a specific doctor by month (current year).
     *
     * @param int $doctorId
     * @param int $month
     * @return int
     */
    public function getEncounterCountByMonth(int $doctorId, int $month): int
    {
        return Encounter::where('doctor_id', $doctorId)
            ->whereMonth('encounter_date', $month)
            ->whereYear('encounter_date', now()->year)
            ->count();
    }

    /**
     * Get recent encounters for a specific doctor.
     *
     * @param int $doctorId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentEncountersByDoctor(int $doctorId, int $limit = 8)
    {
        return Encounter::where('doctor_id', $doctorId)
            ->with(['patient'])
            ->orderBy('encounter_date', 'desc')
            ->orderBy('encounter_time', 'desc')
            ->limit($limit)
            ->get();
    }
}
