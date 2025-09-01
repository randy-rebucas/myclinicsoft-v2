<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphMany;
use Wame\TelInput\TelInput;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\MorphOne;

class Patient extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Patient>
     */
    public static $model = \App\Models\Patient::class;

    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;
    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 5;
    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;
    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['user'];

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
            BelongsTo::make('User'),
            ID::make()->hideFromIndex()->hideFromDetail(),
            Image::make('Image', 'avatar')
                ->disk('public')
                ->path('avatars'),
            Text::make('Name', function () {
                return $this->full_name;
            })->sortable()->hideFromIndex()->hideWhenCreating(),
            Text::make('First Name', 'first_name')->hideFromIndex()->hideFromDetail(),
            Text::make('Last Name', 'last_name')->hideFromIndex()->hideFromDetail(),
            Date::make('Birthday', 'date_of_birth'),
            Select::make('Gender', 'gender')->options([
                'male' => 'Male',
                'female' => 'Female',
                'unknown' => 'Unknown',
            ]),
            TelInput::make('Phone Number', 'phone_number')
                ->onlyCountries(['PH'])
                ->help('International format only e.g. +63'),

            MorphOne::make('Address', 'address'),

            HasMany::make('Encounters', 'encounters'),
            // new Panel('Emergency Contact', [
            //     Text::make('Emergency Contact Name', 'emergency_contact_name')
            //         ->rules('max:255')->hideFromIndex(),
            //     TelInput::make('Emergency Contact Phone', 'emergency_contact_phone')
            //         ->onlyCountries(['PH'])
            //         ->help('International format only e.g. +63')->hideFromIndex(),
            //     Text::make('Relationship to Patient', 'emergency_contact_relationship')
            //         ->rules('max:100')->hideFromIndex(),
            // ]),

            // new Panel('Medical Information', [
            //     Text::make('Blood Type')
            //         ->rules('nullable', 'max:10')->hideFromIndex(),
            //     Text::make('Allergies')
            //         ->rules('nullable', 'max:255')->hideFromIndex(),
            //     Text::make('Chronic Conditions', 'chronic_conditions')
            //         ->rules('nullable', 'max:255')->hideFromIndex(),
            //     Text::make('Current Medications', 'current_medications')
            //         ->rules('nullable', 'max:255')->hideFromIndex(),
            // ]),


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
        return [new Filters\BirthdayFilter];
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
        return [
            ExportAsCsv::make()->withFormat(function ($model) {
                return [
                    'ID' => $model->getKey(),
                    'First Name' => $model->first_name,
                    'Last Name' => $model->last_name,
                    'Birthday' => $model->date_of_birth,
                    'Gender' => $model->gender,
                    'Phone Number' => $model->phone_number,
                    'Address' => $model->address->address_line_1,
                    'Address Line 2' => $model->address->address_line_2,
                    'City' => $model->address->city,
                    'State' => $model->address->state,
                    'Postal Code' => $model->address->postal_code,
                    'Country' => $model->address->country,
                ];
            }),
        ];
    }
}
