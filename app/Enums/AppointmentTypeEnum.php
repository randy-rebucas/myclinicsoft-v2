<?php

namespace App\Enums;

enum AppointmentTypeEnum: string
{
    case CONSULTATION = 'consultation';
    case FOLLOW_UP = 'follow_up';
    case EMERGENCY = 'emergency';
    case ROUTINE_CHECKUP = 'routine_checkup';

    public function label(): string
    {
        return match ($this) {
            static::CONSULTATION => 'Consultation',
            static::FOLLOW_UP => 'Follow Up',
            static::EMERGENCY => 'Emergency',
            static::ROUTINE_CHECKUP => 'Routine Checkup',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
