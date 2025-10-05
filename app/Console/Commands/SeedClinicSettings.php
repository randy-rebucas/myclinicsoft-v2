<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ClinicSettingsSeeder;

class SeedClinicSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clinic:seed-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed clinic settings with default values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding clinic settings...');
        
        $seeder = new ClinicSettingsSeeder();
        $seeder->run();
        
        $this->info('Clinic settings seeded successfully!');
        
        return 0;
    }
}
