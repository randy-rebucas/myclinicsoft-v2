<?php

namespace App\Enums;

enum QueueStatusEnum: string
{
    case WAITING = 'waiting';
    case CALLED = 'called';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            static::WAITING => 'Waiting',
            static::CALLED => 'Called',
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
