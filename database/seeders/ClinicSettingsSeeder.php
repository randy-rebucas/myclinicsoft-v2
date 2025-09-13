<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class ClinicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'clinic_name' => 'Medical Clinic',
            'clinic_address' => '123 Medical Center Dr.',
            'clinic_city' => 'Medical City',
            'clinic_state' => 'MC',
            'clinic_zip' => '12345',
            'clinic_phone' => '(555) 123-4567',
            'clinic_email' => 'info@medicalclinic.com',
            'clinic_website' => 'https://medicalclinic.com',
            'clinic_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM',
            'clinic_emergency' => 'Emergency: (555) 911-0000',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('Clinic settings seeded successfully!');
    }
}
