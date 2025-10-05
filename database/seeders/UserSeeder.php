<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@clinic.com',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create doctor users
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->assignRole('doctor');
            });

        // Create patient users
        User::factory()
            ->count(20)
            ->create()
            ->each(function ($user) {
                $user->assignRole('patient');
            });
    }
}
