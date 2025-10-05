<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointmentDate = fake()->dateTimeBetween('now', '+3 months');
        
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'clinic_id' => Clinic::factory(),
            'appointment_date' => $appointmentDate,
            'appointment_time' => fake()->time('H:i', '08:00', '17:00'),
            'duration' => fake()->randomElement([15, 30, 45, 60]),
            'type' => fake()->randomElement(['consultation', 'follow_up', 'emergency', 'routine_checkup']),
            'status' => fake()->randomElement(['scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show']),
            'notes' => fake()->optional(0.4)->sentence(),
            'cancellation_reason' => fake()->optional(0.1)->randomElement([
                'Patient cancelled',
                'Doctor unavailable',
                'Emergency',
                'Weather',
                'No show'
            ]),
        ];
    }
}
