<?php

namespace App\Enums;

enum PatientStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case DECEASED = 'deceased';

    public function label(): string
    {
        return match ($this) {
            static::ACTIVE => 'Active',
            static::INACTIVE => 'Inactive',
            static::SUSPENDED => 'Suspended',
            static::DECEASED => 'Deceased',
        };
    }

    public function color(): string
    {
        return match ($this) {
            static::ACTIVE => 'green',
            static::INACTIVE => 'gray',
            static::SUSPENDED => 'yellow',
            static::DECEASED => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
