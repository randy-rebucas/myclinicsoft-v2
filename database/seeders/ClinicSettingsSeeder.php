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
            'clinic_name' => 'Kidzklinika',
            'clinic_address' => 'Zone 11, Baybay City, Leyte',
            'clinic_city' => 'Baybay City',
            'clinic_state' => 'Leyte',
            'clinic_zip' => '6521',
            'clinic_country' => 'Philippines',
            'clinic_phone' => '(555) 123-4567',
            'clinic_emergency_phone' => '(555) 999-8888',
            'clinic_email' => 'info@kidzklinika.com',
            'clinic_website' => 'https://kidzklinika.com',
            'clinic_hours_weekdays' => 'Monday - Friday: 8:00 AM - 6:00 PM',
            'clinic_hours_saturday' => 'Saturday: 9:00 AM - 1:00 PM',
            'clinic_hours_sunday' => 'Sunday: Closed',
            'clinic_description' => 'Providing comprehensive pediatric care from newborns to adolescents in a warm, family-friendly environment with cutting-edge technology.',
            'clinic_tagline' => 'Caring for Your Children Like Our Own',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clinic settings seeded successfully
    }
}
