<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Wame\TelInput\TelInput;
use App\Nova\Clinic;
use App\Nova\Patient;

class Doctor extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Doctor>
     */
    public static $model = \App\Models\Doctor::class;

    public static $displayInNavigation = true;
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'first_name';

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
            BelongsTo::make('User')
                ->nullable()
                ->searchable(),

            BelongsTo::make('Clinic')
                ->nullable()
                ->searchable(),

            ID::make()->hideFromIndex()->hideFromDetail(),

            Text::make('First Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255'),

            TelInput::make('Phone', 'phone_number')
                ->onlyCountries(['PH'])
                ->help('International format only e.g. +63')
                ->rules('required', 'max:20'),

            Select::make('Gender')->options([
                'male' => 'Male',
                'female' => 'Female',
                'unknown' => 'Unknown',
            ])->rules('required'),

            Text::make('Specialty')
                ->rules('required', 'max:255'),

            Text::make('License Number')
                ->nullable()
                ->hideFromIndex(),

            Text::make('NPI Number')
                ->nullable()
                ->hideFromIndex(),

            Number::make('Consultation Fee')
                ->step(0.01)
                ->nullable()
                ->hideFromIndex(),

            Textarea::make('Bio')
                ->nullable()
                ->hideFromIndex(),

            KeyValue::make('Available Hours')
                ->nullable()
                ->hideFromIndex(),

            Boolean::make('Is Active')
                ->sortable()
                ->default(true),

            KeyValue::make('Meta')
                ->nullable()
                ->hideFromIndex(),

            BelongsToMany::make('Clinics', 'clinics', Clinic::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Boolean::make('Is Primary', 'is_primary'),
                    ];
                }),

            BelongsToMany::make('Patients', 'patients', Patient::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Boolean::make('Is Active', 'is_active'),
                    ];
                }),

            HasMany::make('Encounters'),
            HasMany::make('Prescriptions'),
            HasMany::make('Queues'),
            HasMany::make('Appointments'),
            MorphMany::make('Activities'),
            MorphMany::make('Addresses'),
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
