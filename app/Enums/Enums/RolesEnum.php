<?php

namespace App\Enums\Enums;

enum RolesEnum: string
{
    case MEDREP = 'medrep';
    case RECEPTIONIST = 'receptionist';
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';
    case ADMIN = 'admin';
    
    // $user->assignRole(RolesEnum::PATIENT);
	// $user->removeRole(RolesEnum::PATIENT);
    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::MEDREP => 'Medical Representatives',
            static::RECEPTIONIST => 'Receptionists',
            static::PATIENT => 'Patients',
            static::DOCTOR => 'Doctors',
            static::ADMIN => 'Admins',
        };
    }
}
