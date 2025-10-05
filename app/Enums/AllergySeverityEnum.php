<?php

namespace App\Enums;

enum AllergySeverityEnum: string
{
    case MILD = 'mild';
    case MODERATE = 'moderate';
    case SEVERE = 'severe';
    case LIFE_THREATENING = 'life_threatening';

    public function label(): string
    {
        return match ($this) {
            static::MILD => 'Mild',
            static::MODERATE => 'Moderate',
            static::SEVERE => 'Severe',
            static::LIFE_THREATENING => 'Life Threatening',
        };
    }

    public function color(): string
    {
        return match ($this) {
            static::MILD => 'green',
            static::MODERATE => 'yellow',
            static::SEVERE => 'orange',
            static::LIFE_THREATENING => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
