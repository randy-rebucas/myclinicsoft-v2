<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Select;

class MedicationItem extends Repeatable
{
    /**
     * Get the fields displayed by the repeatable.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Medication Name')
                ->options([
                    'acetaminophen' => 'Acetaminophen',
                    'ibuprofen' => 'Ibuprofen',
                    'aspirin' => 'Aspirin',
                    // Add more medications as needed
                ])
                ->rules('required'),

            Text::make('Dosage')
                ->rules('required', 'max:255')
                ->help('Enter dosage (e.g., 500mg, 10ml)'),

            Select::make('Frequency')
                ->options([
                    'once_daily' => 'Once Daily',
                    'twice_daily' => 'Twice Daily',
                    'three_times_daily' => 'Three Times Daily',
                    'four_times_daily' => 'Four Times Daily',
                    'as_needed' => 'As Needed',
                    'every_4_hours' => 'Every 4 Hours',
                    'every_6_hours' => 'Every 6 Hours',
                    'every_8_hours' => 'Every 8 Hours',
                ])
                ->rules('required'),
        ];
    }
}
