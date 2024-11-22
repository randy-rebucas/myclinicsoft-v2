<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;

class MedicationItem extends Repeatable
{
    /**
	 * The underlying model the repeatable represents.
	 *
	 * @var class-string
	 */
	public static $model = \App\Models\MedicationItems::class;

    /**
     * Get the fields displayed by the repeatable.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::hidden('id'), // The unique ID field
            Text::make('Medication Name')
                ->rules('required', 'max:255'),

            Text::make('Dosage')
                ->rules('required', 'max:255'),

            Text::make('Frequency')
                ->rules('required', 'max:255'),
        ];
    }
}
