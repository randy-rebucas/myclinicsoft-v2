<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default clinic
        Clinic::factory()->create([
            'name' => 'Main Medical Center',
            'address' => '123 Healthcare Street',
            'city' => 'Manila',
            'state' => 'CA',
            'zip' => '1000',
            'phone' => '+63-2-1234-5678',
            'email' => 'info@mainmedical.com',
            'website' => 'https://mainmedical.com',
            'license_number' => 'CLINIC001',
            'is_active' => true,
        ]);

        // Create additional clinics
        Clinic::factory()->count(2)->create();
    }
}
