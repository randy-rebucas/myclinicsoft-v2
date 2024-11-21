<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Wame\TelInput\TelInput;

class Doctor extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Doctor>
     */
    public static $model = \App\Models\Doctor::class;
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'User Management';
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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
            BelongsTo::make('User')->noPeeking(),

            ID::make()->hideFromIndex()->hideFromDetail(),
            Text::make('First Name')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255'),
            TelInput::make('Phone', 'phone_number')->onlyCountries(['PH'])->help(
                'International format only e.g. +63'
            ),

            Select::make('Gender', 'gender')->options([
                'male' => 'Male',
                'female' => 'Female',
                'unknown' => 'Unknown',
            ]),
            Select::make('Specialty')
                ->options([
                    'general' => 'General Practice',
                    'cardiology' => 'Cardiology',
                    'dermatology' => 'Dermatology',
                    'pediatrics' => 'Pediatrics',
                    'neurology' => 'Neurology',
                    'orthopedics' => 'Orthopedics',
                    'psychiatry' => 'Psychiatry',
                    'ophthalmology' => 'Ophthalmology',
                    'gynecology' => 'Obstetrics & Gynecology',
                    'urology' => 'Urology',
                    'ent' => 'Ear, Nose & Throat',
                    'endocrinology' => 'Endocrinology',
                    'gastroenterology' => 'Gastroenterology',
                    'oncology' => 'Oncology',
                    'pulmonology' => 'Pulmonology',
                    'rheumatology' => 'Rheumatology',
                    'nephrology' => 'Nephrology',
                    'allergy' => 'Allergy & Immunology',
                    'emergency' => 'Emergency Medicine',
                    'family' => 'Family Medicine',
                    'internal' => 'Internal Medicine',
                    'anesthesiology' => 'Anesthesiology',
                    'radiology' => 'Radiology',
                    'surgery' => 'General Surgery',
                    'plastic' => 'Plastic Surgery',
                    'dental' => 'Dental Surgery'
                ])
                ->rules('required'),
            Boolean::make('Is Active')
                ->sortable(),
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
}
