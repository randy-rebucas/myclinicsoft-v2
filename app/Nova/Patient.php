<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Country;
use Wame\TelInput\TelInput;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;

class Patient extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Patient>
     */
    public static $model = \App\Models\Patient::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'first_name';

    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'first_name',
        'last_name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex()->hideFromDetail(),
            Avatar::make('Image', 'avatar'),
            Text::make('Name', function () {
                return $this->first_name.' '.$this->last_name;
            })->sortable()->hideWhenCreating(),
            Text::make('First Name', 'first_name')->hideFromIndex()->hideFromDetail(),
            Text::make('Last Name', 'last_name')->hideFromIndex()->hideFromDetail(),
            Date::make('Birthday', 'date_of_birth'),
            Select::make('Gender', 'gender')->options([
                'male' => 'Male',
                'female' => 'Female',
                'unknown' => 'Unknown',
            ]),
            TelInput::make('phone', 'phone_number')->onlyCountries(['PH'])->help(
                'International format only e.g. +63'
            ),

            Panel::make('Address Information', $this->addressFields()),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function addressFields()
    {
        return [
            Text::make('Address', 'address_line_1')->hideFromIndex(),
            Text::make('Address Line 2')->hideFromIndex(),
            Text::make('City')->hideFromIndex(),
            Text::make('State')->hideFromIndex(),
            Text::make('Postal Code')->hideFromIndex(),
            Country::make('Country')->hideFromIndex(),
        ];
    }
}
