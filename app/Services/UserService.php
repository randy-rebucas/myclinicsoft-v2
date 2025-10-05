<?php

namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Traits\GeneratesUserCredentials;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    use GeneratesUserCredentials;

    /**
     * Create a new user with patient profile.
     *
     * @param array $userData
     * @param array $patientData
     * @return User
     */
    public function createPatientUser(array $userData, array $patientData): User
    {
        return DB::transaction(function () use ($userData, $patientData) {
            // Generate credentials if not provided
            if (!isset($userData['email'])) {
                $credentials = $this->generateCredentials($patientData['first_name'], $patientData['last_name']);
                $userData['email'] = $credentials['email'];
            }

            // Create user
            $user = User::create([
                'name' => $userData['name'] ?? ($patientData['first_name'] . ' ' . $patientData['last_name']),
                'email' => $userData['email'],
                'password' => Hash::make($userData['password'] ?? 'password123'),
                'phone' => $userData['phone'] ?? $patientData['phone_number'],
                'is_active' => $userData['is_active'] ?? true,
            ]);

            // Create patient profile
            $patient = Patient::create(array_merge($patientData, [
                'user_id' => $user->id,
            ]));

            return $user->load('patient');
        });
    }

    /**
     * Create a new user with doctor profile.
     *
     * @param array $userData
     * @param array $doctorData
     * @return User
     */
    public function createDoctorUser(array $userData, array $doctorData): User
    {
        return DB::transaction(function () use ($userData, $doctorData) {
            // Generate credentials if not provided
            if (!isset($userData['email'])) {
                $credentials = $this->generateCredentials($doctorData['first_name'], $doctorData['last_name']);
                $userData['email'] = $credentials['email'];
            }

            // Create user
            $user = User::create([
                'name' => $userData['name'] ?? ($doctorData['first_name'] . ' ' . $doctorData['last_name']),
                'email' => $userData['email'],
                'password' => Hash::make($userData['password'] ?? 'password123'),
                'phone' => $userData['phone'] ?? $doctorData['phone_number'],
                'is_active' => $userData['is_active'] ?? true,
            ]);

            // Create doctor profile
            $doctor = Doctor::create(array_merge($doctorData, [
                'user_id' => $user->id,
            ]));

            return $user->load('doctor');
        });
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $userData
     * @return User
     */
    public function updateUser(User $user, array $userData): User
    {
        $user->update($userData);
        return $user->fresh();
    }

    /**
     * Deactivate a user.
     *
     * @param User $user
     * @return User
     */
    public function deactivateUser(User $user): User
    {
        $user->update(['is_active' => false]);
        return $user;
    }

    /**
     * Activate a user.
     *
     * @param User $user
     * @return User
     */
    public function activateUser(User $user): User
    {
        $user->update(['is_active' => true]);
        return $user;
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $newPassword
     * @return User
     */
    public function updatePassword(User $user, string $newPassword): User
    {
        $user->update(['password' => Hash::make($newPassword)]);
        return $user;
    }

    /**
     * Get user with their profile (patient or doctor).
     *
     * @param User $user
     * @return User
     */
    public function getUserWithProfile(User $user): User
    {
        return $user->load(['patient', 'doctor']);
    }

    /**
     * Search users by name or email.
     *
     * @param string $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchUsers(string $query, int $limit = 10)
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }

    /**
     * Get users by role.
     *
     * @param string $role
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $role, int $limit = 50)
    {
        return User::role($role)
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }

    /**
     * Update last login timestamp.
     *
     * @param User $user
     * @return User
     */
    public function updateLastLogin(User $user): User
    {
        $user->update(['last_login_at' => now()]);
        return $user;
    }
}
