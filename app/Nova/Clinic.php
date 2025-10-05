<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wame\TelInput\TelInput;
use App\Nova\Doctor;

class Clinic extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Clinic>
     */
    public static $model = \App\Models\Clinic::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'city',
        'email',
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
            ID::make()->sortable(),
            
            Text::make('Name')
                ->rules('required', 'max:255'),
                
            Text::make('Address')
                ->rules('required', 'max:255'),
                
            Text::make('City')
                ->rules('required', 'max:100'),
                
            Text::make('State')
                ->default('PH')
                ->rules('required', 'max:100'),
                
            Number::make('Zip')
                ->rules('required', 'max:20'),
                
            TelInput::make('Phone')
                ->onlyCountries(['PH'])
                ->help('International format only e.g. +63')
                ->rules('required', 'max:20'),
                
            Email::make('Email')
                ->rules('required', 'email', 'max:255'),
                
            Text::make('Website')
                ->nullable()
                ->hideFromIndex(),
                
            Text::make('License Number')
                ->nullable()
                ->hideFromIndex(),
                
            Text::make('Tax ID')
                ->nullable()
                ->hideFromIndex(),
                
            Text::make('Logo')
                ->nullable()
                ->hideFromIndex(),
                
            KeyValue::make('Operating Hours')
                ->nullable()
                ->hideFromIndex(),
                
            Text::make('Emergency Contact')
                ->nullable()
                ->hideFromIndex(),
                
            Markdown::make('Description')
                ->nullable()
                ->hideFromIndex(),
                
            Boolean::make('Is Active')
                ->default(true),

            BelongsToMany::make('Doctors', 'doctors', Doctor::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Boolean::make('Is Primary', 'is_primary'),
                    ];
                }),
                
            HasMany::make('Queues'),
            HasMany::make('Appointments'),
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
