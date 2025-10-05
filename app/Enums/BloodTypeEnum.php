<?php

namespace App\Enums;

enum BloodTypeEnum: string
{
    case A_POSITIVE = 'A+';
    case A_NEGATIVE = 'A-';
    case B_POSITIVE = 'B+';
    case B_NEGATIVE = 'B-';
    case AB_POSITIVE = 'AB+';
    case AB_NEGATIVE = 'AB-';
    case O_POSITIVE = 'O+';
    case O_NEGATIVE = 'O-';

    public function label(): string
    {
        return match ($this) {
            static::A_POSITIVE => 'A+',
            static::A_NEGATIVE => 'A-',
            static::B_POSITIVE => 'B+',
            static::B_NEGATIVE => 'B-',
            static::AB_POSITIVE => 'AB+',
            static::AB_NEGATIVE => 'AB-',
            static::O_POSITIVE => 'O+',
            static::O_NEGATIVE => 'O-',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
