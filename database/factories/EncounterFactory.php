<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Encounter>
 */
class EncounterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $encounterDate = fake()->dateTimeBetween('-6 months', 'now');
        
        return [
            'chief_complaint' => fake()->randomElement([
                'Chest pain',
                'Headache',
                'Fever',
                'Cough',
                'Abdominal pain',
                'Back pain',
                'Shortness of breath',
                'Dizziness',
                'Nausea',
                'Fatigue'
            ]),
            'encounter_date' => $encounterDate,
            'encounter_time' => fake()->time(),
            'appointment_type' => fake()->randomElement(['consultation', 'follow_up', 'emergency', 'routine_checkup']),
            'duration' => fake()->randomElement([15, 30, 45, 60]),
            'diagnosis' => fake()->optional(0.8)->randomElement([
                'Hypertension',
                'Diabetes Type 2',
                'Upper respiratory infection',
                'Gastroenteritis',
                'Migraine',
                'Anxiety',
                'Depression',
                'Arthritis'
            ]),
            'treatment_plan' => fake()->optional(0.7)->paragraph(),
            'follow_up_date' => fake()->optional(0.6)->dateTimeBetween('+1 week', '+3 months'),
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show']),
            'notes' => fake()->optional(0.6)->paragraph(),
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
        ];
    }
}
