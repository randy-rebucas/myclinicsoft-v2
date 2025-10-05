<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Number;

class VitalItem extends Repeatable
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
            Text::make('Blood Pressure')
                ->nullable()
                ->help('Systolic/Diastolic in mmHg'),

            Number::make('Heart Rate')
                ->nullable()
                ->help('Beats per minute (BPM)'),

            Number::make('Temperature')
                ->nullable()
                ->step(0.1)
                ->help('Temperature in °C'),

            Number::make('Respiratory Rate')
                ->nullable()
                ->help('Breaths per minute'),

            Number::make('Oxygen Saturation')
                ->nullable()
                ->help('SpO2 percentage')
                ->min(0)
                ->max(100),

            Number::make('Blood Sugar')
                ->nullable()
                ->help('Blood glucose level in mg/dL'),
        ];
    }
}
