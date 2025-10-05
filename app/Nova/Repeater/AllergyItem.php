<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;

class AllergyItem extends Repeatable
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
            Text::make('Allergen')
                ->rules('required', 'max:255'),

            Text::make('Reaction')
                ->rules('required', 'max:255'),

            Select::make('Severity')->options([
                'mild' => 'Mild',
                'moderate' => 'Moderate',
                'severe' => 'Severe',
            ])->rules('required'),

            Textarea::make('Notes')
                ->nullable()
                ->rows(3),
        ];
    }
}
