<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with patient role
        $patientUsers = User::role('patient')->get();
        
        // Create patient profiles for each patient user
        $patientUsers->each(function ($user) {
            Patient::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
