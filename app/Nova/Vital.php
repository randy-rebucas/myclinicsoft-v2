<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class Vital extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Vital>
     */
    public static $model = \App\Models\Vital::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Health Records';

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
            ID::make()->hideFromIndex()->hideFromDetail(),

            BelongsTo::make('Patient')
                ->required()
                ->searchable(),

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

            MorphMany::make('Activities'),
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
