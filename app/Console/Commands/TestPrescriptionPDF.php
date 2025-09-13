<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Medication;
use App\Models\Setting;
use App\Nova\Actions\PrintPrescriptionPDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class TestPrescriptionPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:prescription-pdf {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test prescription PDF generation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Prescription PDF Generation...');

        try {
            // Get or create test user
            $userId = $this->option('user-id');
            if ($userId) {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found.");
                    return 1;
                }
            } else {
                $user = User::first();
                if (!$user) {
                    $this->error('No users found in the database.');
                    return 1;
                }
            }

            $this->info("Using user: {$user->email}");

            // Check if user has doctor profile
            $doctor = $user->doctor;
            if (!$doctor) {
                $this->error('User does not have a doctor profile.');
                return 1;
            }

            $this->info("Doctor: {$doctor->full_name}");

            // Get or create test patient
            $patient = Patient::first();
            if (!$patient) {
                $this->error('No patients found in the database.');
                return 1;
            }

            $this->info("Patient: {$patient->full_name}");

            // Get or create test medication
            $medication = Medication::where('patient_id', $patient->id)->first();
            if (!$medication) {
                $this->info('Creating test medication...');
                $medication = $this->createTestMedication($patient);
            }

            $this->info("Medication ID: {$medication->id}");

            // Ensure settings exist
            $this->ensureSettings();

            // Test PDF generation
            $this->info('Generating PDF...');
            
            // Create action instance
            $action = new PrintPrescriptionPDF();
            
            // Simulate the action
            $result = $action->handle(
                new \Laravel\Nova\Fields\ActionFields([], []),
                Collection::make([$medication])
            );

            if ($result instanceof \Laravel\Nova\Actions\ActionResponse) {
                $this->info('PDF generated successfully!');
                $this->info('Response type: ' . get_class($result));
            } else {
                $this->error('PDF generation failed: ' . $result);
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->info('Test completed successfully!');
        return 0;
    }

    /**
     * Create test medication
     */
    private function createTestMedication($patient)
    {
        return Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Test Medication 500mg',
                        'generic_name' => 'Test Generic',
                        'dosage' => '1 tablet',
                        'frequency' => 'Every 8 hours',
                        'duration' => '7 days',
                        'special_instructions' => 'Take with food',
                        'refills' => 1
                    ]
                ]
            ],
            'notes' => 'Test prescription for testing purposes.',
            'created_at' => now()
        ]);
    }

    /**
     * Ensure required settings exist
     */
    private function ensureSettings()
    {
        $defaultSettings = [
            'clinic_name' => 'Test Medical Clinic',
            'clinic_address' => '123 Test Street',
            'clinic_city' => 'Test City',
            'clinic_state' => 'TS',
            'clinic_zip' => '12345',
            'clinic_phone' => '(555) 123-4567',
            'clinic_email' => 'test@medicalclinic.com',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->info('Settings configured.');
    }
}
