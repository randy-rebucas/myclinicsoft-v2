<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinic>
 */
class ClinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Medical Center',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->randomElement(['CA', 'NY', 'TX', 'FL', 'WA']),
            'zip' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->optional(0.7)->url(),
            'license_number' => fake()->unique()->numerify('CLINIC#######'),
            'tax_id' => fake()->optional(0.8)->numerify('##-#######'),
            'logo' => null,
            'operating_hours' => json_encode([
                'monday' => ['start' => '08:00', 'end' => '18:00'],
                'tuesday' => ['start' => '08:00', 'end' => '18:00'],
                'wednesday' => ['start' => '08:00', 'end' => '18:00'],
                'thursday' => ['start' => '08:00', 'end' => '18:00'],
                'friday' => ['start' => '08:00', 'end' => '18:00'],
                'saturday' => ['start' => '09:00', 'end' => '15:00'],
                'sunday' => ['start' => 'closed', 'end' => 'closed'],
            ]),
            'emergency_contact' => fake()->optional(0.9)->phoneNumber(),
            'description' => fake()->optional(0.8)->paragraph(),
            'is_active' => true,
        ];
    }
}
