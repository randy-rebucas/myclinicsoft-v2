<?php

namespace Database\Seeders;

use App\Models\Practice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PracticeSeeder extends Seeder
{
    public function run(): void
    {
        $practices = [
            'General Practice',
            'Cardiology',
            'Dermatology',
            'Pediatrics',
            'Neurology',
            'Orthopedics',
            'Psychiatry',
            'Ophthalmology',
            'Obstetrics & Gynecology',
            'Urology',
            'Ear, Nose & Throat',
            'Endocrinology',
            'Gastroenterology',
            'Oncology',
            'Pulmonology',
            'Rheumatology',
            'Nephrology',
            'Allergy & Immunology',
            'Emergency Medicine',
            'Family Medicine',
            'Internal Medicine',
            'Anesthesiology',
            'Radiology',
            'General Surgery',
            'Plastic Surgery',
            'Dental Surgery',
        ];

        foreach ($practices as $name) {
            Practice::firstOrCreate(
                ['slug' => Str::slug($name, '-')],
                [
                    'title' => $name,
                    'slug' => Str::slug($name, '-'),
                ]
            );
        }
    }
}
