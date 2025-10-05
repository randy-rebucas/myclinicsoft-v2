<?php

namespace App\Enums;

enum RolesEnum: string
{
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';
    case ADMIN = 'admin';
    
    // $user->assignRole(RolesEnum::PATIENT);
	// $user->removeRole(RolesEnum::PATIENT);
    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::PATIENT => 'Patients',
            static::DOCTOR => 'Doctors',
            static::ADMIN => 'Admins',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
