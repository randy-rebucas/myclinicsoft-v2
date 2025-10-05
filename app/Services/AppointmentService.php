<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
    /**
     * Create a new appointment.
     *
     * @param Patient $patient
     * @param Doctor $doctor
     * @param Clinic $clinic
     * @param Carbon $appointmentDate
     * @param string $appointmentTime
     * @param int $duration
     * @param string $type
     * @param string|null $notes
     * @return Appointment
     */
    public function createAppointment(
        Patient $patient,
        Doctor $doctor,
        Clinic $clinic,
        Carbon $appointmentDate,
        string $appointmentTime,
        int $duration = 30,
        string $type = 'consultation',
        ?string $notes = null
    ): Appointment {
        return Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'duration' => $duration,
            'type' => $type,
            'status' => 'scheduled',
            'notes' => $notes,
        ]);
    }

    /**
     * Confirm an appointment.
     *
     * @param Appointment $appointment
     * @return Appointment
     */
    public function confirmAppointment(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'confirmed']);
        return $appointment;
    }

    /**
     * Start an appointment.
     *
     * @param Appointment $appointment
     * @return Appointment
     */
    public function startAppointment(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'in_progress']);
        return $appointment;
    }

    /**
     * Complete an appointment.
     *
     * @param Appointment $appointment
     * @return Appointment
     */
    public function completeAppointment(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'completed']);
        return $appointment;
    }

    /**
     * Cancel an appointment.
     *
     * @param Appointment $appointment
     * @param string|null $reason
     * @return Appointment
     */
    public function cancelAppointment(Appointment $appointment, ?string $reason = null): Appointment
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
        return $appointment;
    }

    /**
     * Mark appointment as no-show.
     *
     * @param Appointment $appointment
     * @return Appointment
     */
    public function markNoShow(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => 'no_show']);
        return $appointment;
    }

    /**
     * Get available time slots for a doctor on a specific date.
     *
     * @param Doctor $doctor
     * @param Carbon $date
     * @param int $duration
     * @return Collection
     */
    public function getAvailableTimeSlots(Doctor $doctor, Carbon $date, int $duration = 30): Collection
    {
        $startTime = $date->copy()->setTime(9, 0); // 9:00 AM
        $endTime = $date->copy()->setTime(17, 0); // 5:00 PM
        
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->get()
            ->map(function ($appointment) {
                return [
                    'start' => Carbon::parse($appointment->appointment_time),
                    'end' => Carbon::parse($appointment->appointment_time)->addMinutes($appointment->duration),
                ];
            });

        $availableSlots = collect();
        $current = $startTime->copy();

        while ($current->lt($endTime)) {
            $slotEnd = $current->copy()->addMinutes($duration);
            
            $isBooked = $bookedSlots->contains(function ($slot) use ($current, $slotEnd) {
                return $current->lt($slot['end']) && $slotEnd->gt($slot['start']);
            });

            if (!$isBooked) {
                $availableSlots->push($current->format('H:i'));
            }

            $current->addMinutes($duration);
        }

        return $availableSlots;
    }

    /**
     * Get appointments for a doctor on a specific date.
     *
     * @param Doctor $doctor
     * @param Carbon $date
     * @return Collection
     */
    public function getDoctorAppointments(Doctor $doctor, Carbon $date): Collection
    {
        return Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->with(['patient', 'clinic'])
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get upcoming appointments for a patient.
     *
     * @param Patient $patient
     * @param int $days
     * @return Collection
     */
    public function getUpcomingAppointments(Patient $patient, int $days = 30): Collection
    {
        return Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->where('appointment_date', '<=', now()->addDays($days))
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with(['doctor', 'clinic'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }
}
