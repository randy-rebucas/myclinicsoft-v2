<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        // create demo users
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            // PermissionsSeeder::class,
            // PatientSeeder::class,
            // DoctorSeeder::class,
            // ReceptionistSeeder::class,
            // MedRepresentativeSeeder::class
        ]);
    }
}
