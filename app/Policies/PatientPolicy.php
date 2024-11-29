<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Patient;

class PatientPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['doctor', 'receptionist', 'admin']);
    }

    public function view(User $user, Patient $patient)
    {
        return in_array($user->role, ['doctor', 'receptionist', 'admin']) ||
            ($user->role === 'patient' && $user->id === $patient->user_id);
    }

    public function create(User $user)
    {
        return in_array($user->role, ['receptionist', 'admin']);
    }

    public function update(User $user, Patient $patient)
    {
        return in_array($user->role, ['doctor', 'receptionist', 'admin']);
    }

    public function delete(User $user)
    {
        return $user->role === 'admin';
    }
}
