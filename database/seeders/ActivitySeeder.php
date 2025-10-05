<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Encounter;
use App\Models\MedicalCondition;
use App\Models\Medication;
use App\Models\Queue;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $patients = Patient::all();
        $doctors = Doctor::all();
        $encounters = Encounter::all();
        $medicalConditions = MedicalCondition::all();
        $medications = Medication::all();
        $queues = Queue::all();

        // Create activities for patients
        foreach ($patients as $patient) {
            $this->createPatientActivities($patient, $users);
        }

        // Create activities for doctors
        foreach ($doctors as $doctor) {
            $this->createDoctorActivities($doctor, $users);
        }


        // Create activities for encounters
        foreach ($encounters as $encounter) {
            $this->createEncounterActivities($encounter, $users);
        }

        // Create activities for medical conditions
        foreach ($medicalConditions as $condition) {
            $this->createMedicalConditionActivities($condition, $users);
        }

        // Create activities for medications
        foreach ($medications as $medication) {
            $this->createMedicationActivities($medication, $users);
        }

        // Create activities for queue
        foreach ($queues as $queue) {
            $this->createQueueActivities($queue, $users);
        }
    }

    private function createPatientActivities($patient, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Patient record was created',
                'changes' => ['status' => 'new_patient']
            ],
            [
                'type' => 'updated',
                'description' => 'Patient information was updated',
                'changes' => ['field' => 'contact_info']
            ],
            [
                'type' => 'updated',
                'description' => 'Medical history was updated',
                'changes' => ['field' => 'medical_history']
            ],
            [
                'type' => 'updated',
                'description' => 'Insurance information was updated',
                'changes' => ['field' => 'insurance']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                    'subject_type' => Patient::class,
                    'subject_id' => $patient->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function createDoctorActivities($doctor, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Doctor profile was created',
                'changes' => ['status' => 'new_doctor']
            ],
            [
                'type' => 'assigned role doctor',
                'description' => 'Doctor role was assigned',
                'changes' => ['role' => 'doctor']
            ],
            [
                'type' => 'updated',
                'description' => 'Doctor profile was updated',
                'changes' => ['field' => 'specialty']
            ],
            [
                'type' => 'updated',
                'description' => 'Doctor availability was updated',
                'changes' => ['field' => 'schedule']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                'subject_type' => Doctor::class,
                'subject_id' => $doctor->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }



    private function createEncounterActivities($encounter, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Medical encounter was created',
                'changes' => ['encounter_type' => 'consultation']
            ],
            [
                'type' => 'updated',
                'description' => 'Encounter notes were updated',
                'changes' => ['field' => 'notes']
            ],
            [
                'type' => 'updated',
                'description' => 'Diagnosis was added to encounter',
                'changes' => ['field' => 'diagnosis']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                'subject_type' => Encounter::class,
                'subject_id' => $encounter->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function createMedicalConditionActivities($condition, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Medical condition was diagnosed',
                'changes' => ['condition' => $condition->condition_name]
            ],
            [
                'type' => 'updated',
                'description' => 'Treatment plan was updated',
                'changes' => ['field' => 'treatment_plan']
            ],
            [
                'type' => 'updated',
                'description' => 'Condition status was updated',
                'changes' => ['field' => 'status']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                'subject_type' => MedicalCondition::class,
                'subject_id' => $condition->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function createMedicationActivities($medication, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Medication was prescribed',
                'changes' => ['prescription_type' => 'new_prescription']
            ],
            [
                'type' => 'updated',
                'description' => 'Medication dosage was adjusted',
                'changes' => ['field' => 'dosage']
            ],
            [
                'type' => 'updated',
                'description' => 'Medication notes were updated',
                'changes' => ['field' => 'notes']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                'subject_type' => Medication::class,
                'subject_id' => $medication->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
            ]);
        }
    }

    private function createQueueActivities($queue, $users)
    {
        $activities = [
            [
                'type' => 'created',
                'description' => 'Patient was added to queue',
                'changes' => ['queue_number' => $queue->queue_number]
            ],
            [
                'type' => 'updated',
                'description' => 'Queue status was updated',
                'changes' => ['field' => 'status']
            ],
            [
                'type' => 'updated',
                'description' => 'Patient was called',
                'changes' => ['field' => 'called_at']
            ],
            [
                'type' => 'updated',
                'description' => 'Appointment was completed',
                'changes' => ['field' => 'completed_at']
            ]
        ];

        foreach ($activities as $activity) {
            Activity::create([
                'subject_type' => Queue::class,
                'subject_id' => $queue->id,
                'type' => $activity['type'],
                'description' => $activity['description'],
                'changes' => $activity['changes'],
                'causer_id' => $users->random()->id,
                'created_at' => now()->subDays(rand(1, 30))
                ]);
        }
    }
}
