<?php

namespace Database\Seeders;

use App\Models\MedRepresentative;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedRepresentativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(3)
            ->has(MedRepresentative::factory())
            ->create();
    }
}
