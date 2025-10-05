<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with doctor role
        $doctorUsers = User::role('doctor')->get();
        
        // Create doctor profiles for each doctor user
        $doctorUsers->each(function ($user) {
            Doctor::factory()->create([
                'user_id' => $user->id,
                'clinic_id' => \App\Models\Clinic::first()?->id ?? \App\Models\Clinic::factory()->create()->id,
            ]);
        });
    }
}
