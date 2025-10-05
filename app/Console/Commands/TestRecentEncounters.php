<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Services\EncounterService;
use Illuminate\Console\Command;

class TestRecentEncounters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:recent-encounters {doctor_id=1} {limit=8}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the recent encounters query for a specific doctor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $doctorId = $this->argument('doctor_id');
        $limit = $this->argument('limit');
        $encounterService = app(EncounterService::class);

        // Check if doctor exists
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            $this->error("Doctor with ID {$doctorId} not found.");
            return 1;
        }

        $this->info("Testing recent encounters for Doctor: {$doctor->user->name} (ID: {$doctorId})");
        $this->info("Limit: {$limit}");

        // Get recent encounters
        $recentEncounters = $encounterService->getRecentEncountersByDoctor($doctorId, $limit);

        if ($recentEncounters->isEmpty()) {
            $this->warn("No encounters found for this doctor.");
            return 0;
        }

        $this->info("\nRecent Encounters:");
        $this->info("==================");

        $headers = ['ID', 'Patient', 'Date', 'Time', 'Status', 'Chief Complaint'];
        $rows = [];

        foreach ($recentEncounters as $encounter) {
            $rows[] = [
                $encounter->id,
                $encounter->patient->full_name ?? 'N/A',
                $encounter->encounter_date?->format('Y-m-d') ?? 'N/A',
                $encounter->encounter_time ?? 'N/A',
                $encounter->status ?? 'N/A',
                $encounter->chief_complaint ?? 'N/A'
            ];
        }

        $this->table($headers, $rows);

        // Show raw SQL equivalent
        $this->info("\nRaw SQL equivalent:");
        $this->info("SELECT * FROM encounters WHERE doctor_id = {$doctorId} ORDER BY encounter_date DESC, encounter_time DESC LIMIT {$limit}");

        return 0;
    }
}