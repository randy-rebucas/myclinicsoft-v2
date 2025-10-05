<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            static::MALE => 'Male',
            static::FEMALE => 'Female',
            static::UNKNOWN => 'Unknown',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
