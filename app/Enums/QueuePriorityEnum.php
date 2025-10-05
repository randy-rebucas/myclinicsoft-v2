<?php

namespace App\Enums;

enum QueuePriorityEnum: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            static::LOW => 'Low',
            static::NORMAL => 'Normal',
            static::HIGH => 'High',
            static::URGENT => 'Urgent',
            static::EMERGENCY => 'Emergency',
        };
    }

    public function color(): string
    {
        return match ($this) {
            static::LOW => 'gray',
            static::NORMAL => 'blue',
            static::HIGH => 'yellow',
            static::URGENT => 'orange',
            static::EMERGENCY => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
