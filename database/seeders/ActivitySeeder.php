<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $patients = Patient::all();
        $users = User::all();

        foreach ($patients as $patient) {
            Activity::factory()
                ->count(3)
                ->create([
                    'subject_type' => Patient::class,
                    'subject_id' => $patient->id,
                    'causer_id' => $users->random()->id
                ]);
        }
    }
}
