<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Date;

class PrescriptionItem extends Repeatable
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
                ->rules('required', 'max:100'),

            Text::make('Frequency')
                ->rules('required', 'max:100'),

            Number::make('Quantity')
                ->rules('required', 'integer', 'min:1'),

            Number::make('Refills')
                ->default(0)
                ->rules('required', 'integer', 'min:0'),

            Textarea::make('Instructions')
                ->nullable(),

            Select::make('Status')->options([
                'active' => 'Active',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
            ])->rules('required'),

            Date::make('Start Date')
                ->rules('required'),

            Date::make('End Date')
                ->nullable(),
        ];
    }
}
