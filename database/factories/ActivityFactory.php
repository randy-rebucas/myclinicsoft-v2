<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        $activityTypes = [
            'created', 'updated', 'deleted', 'restored', 'force deleted',
            'assigned role doctor', 'assigned role receptionist', 'assigned role medrep',
            'assigned role patient', 'login', 'logout', 'password changed',
            'profile updated', 'schedule updated', 'appointment created',
            'appointment updated', 'appointment cancelled', 'prescription created',
            'prescription updated', 'diagnosis added', 'treatment plan updated',
            'queue status changed', 'patient called', 'appointment completed'
        ];

        $descriptions = [
            'created' => 'Record was created',
            'updated' => 'Record was updated',
            'deleted' => 'Record was deleted',
            'restored' => 'Record was restored',
            'force deleted' => 'Record was permanently deleted',
            'assigned role doctor' => 'Doctor role was assigned',
            'assigned role patient' => 'Patient role was assigned',
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'password changed' => 'Password was changed',
            'profile updated' => 'Profile information was updated',
            'schedule updated' => 'Schedule was updated',
            'appointment created' => 'New appointment was created',
            'appointment updated' => 'Appointment was updated',
            'appointment cancelled' => 'Appointment was cancelled',
            'prescription created' => 'New prescription was created',
            'prescription updated' => 'Prescription was updated',
            'diagnosis added' => 'New diagnosis was added',
            'treatment plan updated' => 'Treatment plan was updated',
            'queue status changed' => 'Queue status was changed',
            'patient called' => 'Patient was called',
            'appointment completed' => 'Appointment was completed'
        ];

        $type = $this->faker->randomElement($activityTypes);
        $description = $descriptions[$type] ?? $this->faker->sentence();

        return [
            'type' => $type,
            'description' => $description,
            'changes' => $this->generateChanges($type),
            'causer_id' => User::factory()
        ];
    }

    private function generateChanges($type)
    {
        $changeTypes = [
            'created' => ['status' => 'new'],
            'updated' => ['field' => $this->faker->randomElement(['name', 'email', 'phone', 'address', 'specialty', 'schedule'])],
            'deleted' => ['reason' => $this->faker->randomElement(['user_request', 'admin_action', 'system_cleanup'])],
            'restored' => ['restored_by' => 'admin'],
            'force deleted' => ['reason' => 'permanent_deletion'],
            'assigned role doctor' => ['role' => 'doctor'],
            'assigned role receptionist' => ['role' => 'receptionist'],
            'assigned role medrep' => ['role' => 'medrep'],
            'assigned role patient' => ['role' => 'patient'],
            'login' => ['ip_address' => $this->faker->ipv4(), 'user_agent' => $this->faker->userAgent()],
            'logout' => ['session_duration' => $this->faker->numberBetween(300, 7200)],
            'password changed' => ['changed_by' => 'user'],
            'profile updated' => ['field' => $this->faker->randomElement(['name', 'email', 'phone', 'address'])],
            'schedule updated' => ['field' => $this->faker->randomElement(['availability', 'working_hours', 'breaks'])],
            'appointment created' => ['appointment_type' => $this->faker->randomElement(['consultation', 'follow_up', 'emergency'])],
            'appointment updated' => ['field' => $this->faker->randomElement(['time', 'date', 'notes', 'status'])],
            'appointment cancelled' => ['reason' => $this->faker->randomElement(['patient_request', 'doctor_unavailable', 'emergency'])],
            'prescription created' => ['medication_count' => $this->faker->numberBetween(1, 5)],
            'prescription updated' => ['field' => $this->faker->randomElement(['dosage', 'frequency', 'duration'])],
            'diagnosis added' => ['diagnosis_type' => $this->faker->randomElement(['primary', 'secondary', 'differential'])],
            'treatment plan updated' => ['field' => $this->faker->randomElement(['medication', 'therapy', 'lifestyle'])],
            'queue status changed' => ['old_status' => 'waiting', 'new_status' => $this->faker->randomElement(['called', 'in_progress', 'completed'])],
            'patient called' => ['queue_number' => $this->faker->numberBetween(1, 100)],
            'appointment completed' => ['duration' => $this->faker->numberBetween(15, 120)]
        ];

        return $changeTypes[$type] ?? ['field' => $this->faker->word()];
    }
}
