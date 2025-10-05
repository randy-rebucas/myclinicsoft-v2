<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use App\Services\EncounterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncounterCountTest extends TestCase
{
    use RefreshDatabase;

    protected EncounterService $encounterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encounterService = app(EncounterService::class);
    }

    public function test_october_encounter_count_for_doctor()
    {
        // Create a doctor
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        
        // Create a patient
        $patient = Patient::factory()->create();

        // Create encounters in October 2024
        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-10-15',
        ]);

        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-10-20',
        ]);

        // Create encounters in other months (should not be counted)
        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-09-15',
        ]);

        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-11-15',
        ]);

        // Test the service method
        $octoberCount = $this->encounterService->getEncounterCountByMonthAndYear($doctor->id, 10, 2024);
        
        $this->assertEquals(2, $octoberCount);
    }

    public function test_current_month_encounter_count()
    {
        // Create a doctor
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        
        // Create a patient
        $patient = Patient::factory()->create();

        // Create encounters for current month
        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => now()->format('Y-m-d'),
        ]);

        // Test the service method
        $currentMonthCount = $this->encounterService->getEncounterCountByMonth($doctor->id, now()->month);
        
        $this->assertEquals(1, $currentMonthCount);
    }

    public function test_encounter_count_returns_zero_when_no_encounters()
    {
        // Create a doctor
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);

        // Test with no encounters
        $octoberCount = $this->encounterService->getEncounterCountByMonthAndYear($doctor->id, 10, 2024);
        
        $this->assertEquals(0, $octoberCount);
    }

    public function test_recent_encounters_by_doctor()
    {
        // Create a doctor
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        
        // Create a patient
        $patient = Patient::factory()->create();

        // Create encounters with different dates
        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-10-15',
            'encounter_time' => '10:00:00',
        ]);

        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-10-20',
            'encounter_time' => '14:00:00',
        ]);

        Encounter::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'encounter_date' => '2024-10-10',
            'encounter_time' => '09:00:00',
        ]);

        // Test the service method
        $recentEncounters = $this->encounterService->getRecentEncountersByDoctor($doctor->id, 8);
        
        $this->assertCount(3, $recentEncounters);
        
        // Check that they are ordered by date desc, then time desc
        $this->assertEquals('2024-10-20', $recentEncounters->first()->encounter_date->format('Y-m-d'));
        $this->assertEquals('2024-10-10', $recentEncounters->last()->encounter_date->format('Y-m-d'));
    }
}