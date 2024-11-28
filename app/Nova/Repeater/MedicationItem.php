<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;

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
            Text::make('Medication Name')
                ->rules('required', 'max:255'),

            Text::make('Dosage')
                ->rules('required', 'max:255'),

            Text::make('Frequency')
                ->rules('required', 'max:255'),
        ];
    }
}
