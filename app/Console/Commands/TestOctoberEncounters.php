<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Services\EncounterService;
use Illuminate\Console\Command;

class TestOctoberEncounters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:october-encounters {doctor_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the October encounter count query for a specific doctor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $doctorId = $this->argument('doctor_id');
        $encounterService = app(EncounterService::class);

        // Check if doctor exists
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            $this->error("Doctor with ID {$doctorId} not found.");
            return 1;
        }

        $this->info("Testing October encounter count for Doctor: {$doctor->user->name} (ID: {$doctorId})");

        // Test the October 2024 count
        $octoberCount = $encounterService->getEncounterCountByMonthAndYear($doctorId, 10, 2024);
        $this->info("October 2024 encounters: {$octoberCount}");

        // Test current month count
        $currentMonthCount = $encounterService->getEncounterCountByMonth($doctorId, now()->month);
        $this->info("Current month ({$this->getMonthName(now()->month)}) encounters: {$currentMonthCount}");

        // Show raw SQL equivalent
        $this->info("\nRaw SQL equivalent:");
        $this->info("SELECT COUNT(*) as aggregate FROM encounters WHERE MONTH(encounter_date) = 10 AND YEAR(encounter_date) = 2024 AND doctor_id = {$doctorId}");

        return 0;
    }

    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return $months[$month] ?? 'Unknown';
    }
}