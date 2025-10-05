<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            static::SCHEDULED => 'Scheduled',
            static::CONFIRMED => 'Confirmed',
            static::IN_PROGRESS => 'In Progress',
            static::COMPLETED => 'Completed',
            static::CANCELLED => 'Cancelled',
            static::NO_SHOW => 'No Show',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
