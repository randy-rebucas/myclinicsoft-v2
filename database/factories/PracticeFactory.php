<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeFactory extends Factory
{
    public function definition(): array
    {
        $practices = [
            'general' => 'General Practice',
            'cardiology' => 'Cardiology',
            'dermatology' => 'Dermatology',
            'pediatrics' => 'Pediatrics',
            'neurology' => 'Neurology',
            'orthopedics' => 'Orthopedics',
            'psychiatry' => 'Psychiatry',
            'ophthalmology' => 'Ophthalmology',
            'gynecology' => 'Obstetrics & Gynecology',
            'urology' => 'Urology',
            'ent' => 'Ear, Nose & Throat',
            'endocrinology' => 'Endocrinology',
            'gastroenterology' => 'Gastroenterology',
            'oncology' => 'Oncology',
            'pulmonology' => 'Pulmonology',
            'rheumatology' => 'Rheumatology',
            'nephrology' => 'Nephrology',
            'allergy' => 'Allergy & Immunology',
            'emergency' => 'Emergency Medicine',
            'family' => 'Family Medicine',
            'internal' => 'Internal Medicine',
            'anesthesiology' => 'Anesthesiology',
            'radiology' => 'Radiology',
            'surgery' => 'General Surgery',
            'plastic' => 'Plastic Surgery',
            'dental' => 'Dental Surgery',
        ];

        $slug = $this->faker->randomElement(array_keys($practices));
        return [
            'title' => $practices[$slug],
        ];
    }
}
