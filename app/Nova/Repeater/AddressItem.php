<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Boolean;

class AddressItem extends Repeatable
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
            Select::make('Label')->options([
                'home' => 'Home',
                'work' => 'Work',
                'other' => 'Other',
            ])->rules('required'),

            Boolean::make('Default')
                ->default(false),

            Text::make('Street Address', 'address_line_1')
                ->rules('required', 'max:255'),

            Text::make('Apartment/Suite', 'address_line_2')
                ->nullable(),

            Text::make('City')
                ->rules('required', 'max:100'),

            Text::make('State')
                ->rules('required', 'max:100'),

            Text::make('Postal Code', 'postal_code')
                ->rules('required', 'max:20'),

            Country::make('Country')
                ->rules('required'),
        ];
    }
}
