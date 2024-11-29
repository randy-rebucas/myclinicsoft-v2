<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Doctor;

class DoctorPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'receptionist']);
    }

    public function view(User $user, Doctor $doctor)
    {
        return in_array($user->role, ['admin', 'receptionist']) ||
            ($user->role === 'doctor' && $user->id === $doctor->user_id);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Doctor $doctor)
    {
        return $user->role === 'admin' ||
            ($user->role === 'doctor' && $user->id === $doctor->user_id);
    }

    public function delete(User $user)
    {
        return $user->role === 'admin';
    }
}
