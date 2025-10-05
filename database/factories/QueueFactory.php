<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Queue>
 */
class QueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'clinic_id' => Clinic::factory(),
            'doctor_id' => Doctor::factory(),
            'queue_number' => fake()->unique()->numerify('Q###'),
            'status' => fake()->randomElement(['waiting', 'called', 'in_progress', 'completed', 'cancelled', 'no_show']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent', 'emergency']),
            'called_at' => fake()->optional(0.3)->dateTimeBetween('-2 hours', 'now'),
            'completed_at' => fake()->optional(0.2)->dateTimeBetween('-1 hour', 'now'),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
