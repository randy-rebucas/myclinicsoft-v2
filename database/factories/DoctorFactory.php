<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'specialty' => fake()->randomElement(['General Medicine', 'Cardiology', 'Dermatology', 'Pediatrics', 'Orthopedics', 'Neurology']),
            'license_number' => fake()->unique()->numerify('MD#######'),
            'npi_number' => fake()->unique()->numerify('##########'),
            'consultation_fee' => fake()->randomFloat(2, 50, 500),
            'bio' => fake()->paragraph(),
            'available_hours' => json_encode([
                'monday' => ['start' => '09:00', 'end' => '17:00'],
                'tuesday' => ['start' => '09:00', 'end' => '17:00'],
                'wednesday' => ['start' => '09:00', 'end' => '17:00'],
                'thursday' => ['start' => '09:00', 'end' => '17:00'],
                'friday' => ['start' => '09:00', 'end' => '17:00'],
            ]),
            'is_active' => true,
            'user_id' => User::factory(),
            'clinic_id' => \App\Models\Clinic::factory(),
            'meta' => null,
        ];
    }
}
