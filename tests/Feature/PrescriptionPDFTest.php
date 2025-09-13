<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Medication;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

class PrescriptionPDFTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test settings
        $this->createTestSettings();
    }

    private function createTestSettings()
    {
        $settings = [
            'clinic_name' => 'Test Medical Clinic',
            'clinic_address' => '456 Test Street',
            'clinic_city' => 'Test City',
            'clinic_state' => 'TS',
            'clinic_zip' => '54321',
            'clinic_phone' => '(555) 987-6543',
            'clinic_email' => 'test@medicalclinic.com',
        ];

        foreach ($settings as $key => $value) {
            Setting::create(['key' => $key, 'value' => $value]);
        }
    }

    public function test_prescription_pdf_generation_with_valid_data()
    {
        // Create test user and doctor
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create([
            'user_id' => $user->id,
            'meta' => [
                'PRC' => 'PRC123456',
                'PTR' => 'PTR789012',
                'S2' => 'S2345678'
            ]
        ]);

        // Create test patient with address
        $patient = Patient::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male'
        ]);

        // Create test medication
        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Amoxicillin 500mg',
                        'generic_name' => 'Amoxicillin',
                        'dosage' => '1 capsule',
                        'frequency' => 'Every 8 hours',
                        'duration' => '7 days',
                        'special_instructions' => 'Take with food',
                        'refills' => 1
                    ]
                ],
                [
                    'fields' => [
                        'medication_name' => 'Ibuprofen 400mg',
                        'generic_name' => 'Ibuprofen',
                        'dosage' => '1 tablet',
                        'frequency' => 'Every 6 hours as needed',
                        'duration' => '5 days',
                        'special_instructions' => 'Take with water',
                        'refills' => 0
                    ]
                ]
            ],
            'notes' => 'Patient has mild infection. Monitor for allergic reactions.',
            'created_at' => now()
        ]);

        // Act as the user
        $this->actingAs($user);

        // Test the action
        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        // Assert response
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition');
    }

    public function test_prescription_pdf_generation_with_missing_patient()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);

        // Create medication without patient
        $medication = Medication::create([
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Test Medication',
                        'dosage' => '1 tablet',
                        'frequency' => 'Daily'
                    ]
                ]
            ]
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        $response->assertStatus(422);
    }

    public function test_prescription_pdf_generation_with_empty_items()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create();

        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => []
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        $response->assertStatus(422);
    }

    public function test_prescription_pdf_generation_with_malformed_items()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create();

        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                'invalid_item_format',
                [
                    'fields' => [
                        'medication_name' => 'Valid Medication',
                        'dosage' => '1 tablet',
                        'frequency' => 'Daily'
                    ]
                ]
            ]
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        // Should still work but skip invalid items
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_prescription_pdf_generation_with_missing_doctor()
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();

        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Test Medication',
                        'dosage' => '1 tablet',
                        'frequency' => 'Daily'
                    ]
                ]
            ]
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        $response->assertStatus(422);
    }

    public function test_prescription_pdf_generation_with_special_characters()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'José',
            'last_name' => 'García'
        ]);

        $patient = Patient::factory()->create([
            'first_name' => 'María',
            'last_name' => 'Rodríguez',
            'date_of_birth' => '1985-05-15',
            'gender' => 'female'
        ]);

        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Paracetamol 500mg (Acetaminophen)',
                        'generic_name' => 'Acetaminophen',
                        'dosage' => '1-2 tablets',
                        'frequency' => 'Every 4-6 hours as needed',
                        'duration' => '3 days',
                        'special_instructions' => 'Take with food, avoid alcohol',
                        'refills' => 0
                    ]
                ]
            ],
            'notes' => 'Patient has headache. Monitor for fever >38°C.',
            'created_at' => now()
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_prescription_pdf_generation_with_long_text()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $patient = Patient::factory()->create();

        $longNote = str_repeat('This is a very long note that tests the PDF generation with extensive text content. ', 50);

        $medication = Medication::create([
            'patient_id' => $patient->id,
            'prescription_items' => [
                [
                    'fields' => [
                        'medication_name' => 'Test Medication with Very Long Name That Might Cause Layout Issues',
                        'generic_name' => 'Generic Name That Is Also Very Long and Might Cause Display Problems',
                        'dosage' => '1 tablet every 12 hours with food and plenty of water',
                        'frequency' => 'Twice daily, morning and evening, preferably at the same time each day',
                        'duration' => '10 days, complete the full course even if symptoms improve',
                        'special_instructions' => 'Avoid grapefruit juice, take on empty stomach, store in refrigerator, keep out of reach of children',
                        'refills' => 2
                    ]
                ]
            ],
            'notes' => $longNote,
            'created_at' => now()
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/nova-api/medications/action", [
            'resources' => [$medication->id],
            'action' => 'print-prescription-p-d-f',
            'fields' => []
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
