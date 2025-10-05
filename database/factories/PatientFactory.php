<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'avatar' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'user_id' => User::factory(),
            
            // Extended patient information
            'secondary_phone' => fake()->optional(0.3)->phoneNumber(),
            'emergency_contact_name' => fake()->optional(0.8)->name(),
            'emergency_contact_relationship' => fake()->optional(0.8)->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),
            'emergency_contact_phone' => fake()->optional(0.8)->phoneNumber(),
            'insurance_provider' => fake()->optional(0.6)->randomElement(['Blue Cross', 'Aetna', 'Cigna', 'UnitedHealth', 'Medicare']),
            'insurance_id' => fake()->optional(0.6)->numerify('INS#######'),
            'primary_physician' => fake()->optional(0.7)->name(),
            'allergies' => fake()->optional(0.4)->randomElement(['Penicillin', 'Shellfish', 'Peanuts', 'Latex', 'Aspirin']),
            'chronic_conditions' => fake()->optional(0.3)->randomElement(['Diabetes', 'Hypertension', 'Asthma', 'Arthritis']),
            'current_medications' => fake()->optional(0.5)->randomElement(['Metformin', 'Lisinopril', 'Albuterol', 'Ibuprofen']),
            'philhealth_number' => fake()->optional(0.7)->numerify('PH#########'),
            'blood_type' => fake()->optional(0.8)->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'height' => fake()->optional(0.9)->randomFloat(2, 150, 200),
            'weight' => fake()->optional(0.9)->randomFloat(2, 50, 120),
            'bmi' => fake()->optional(0.9)->randomFloat(2, 18, 35),
            'occupation' => fake()->optional(0.8)->jobTitle(),
            'civil_status' => fake()->optional(0.8)->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'nationality' => fake()->optional(0.9)->randomElement(['Filipino', 'American', 'Chinese', 'Japanese', 'Korean']),
            'religion' => fake()->optional(0.7)->randomElement(['Catholic', 'Protestant', 'Muslim', 'Buddhist', 'Hindu', 'None']),
            
            // Administrative
            'status' => fake()->randomElement(['active', 'inactive', 'suspended']),
            'mrn' => fake()->unique()->numerify('MRN#######'),
            
            // Risk Assessment
            'risk_level' => fake()->optional(0.6)->randomElement(['low', 'medium', 'high']),
            'alerts' => null,
            'fall_risk' => fake()->optional(0.3)->randomElement(['low', 'medium', 'high']),
            'dietary_restrictions' => fake()->optional(0.4)->randomElement(['Vegetarian', 'Vegan', 'Gluten-free', 'Diabetic diet']),
            
            // Extended Medical History
            'family_history' => fake()->optional(0.5)->randomElement(['Heart disease', 'Diabetes', 'Cancer', 'Hypertension']),
            'surgical_history' => fake()->optional(0.3)->randomElement(['Appendectomy', 'Gallbladder removal', 'Knee surgery']),
            'smoking_status' => fake()->optional(0.8)->randomElement(['Never', 'Former', 'Current']),
            'alcohol_use' => fake()->optional(0.8)->randomElement(['None', 'Occasional', 'Regular']),
            'exercise_habits' => fake()->optional(0.7)->randomElement(['Sedentary', 'Light', 'Moderate', 'Active']),
            'immunizations' => null,
            'last_physical_date' => fake()->optional(0.7)->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
