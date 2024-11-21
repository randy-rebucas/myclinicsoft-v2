<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;

class MedicalCondition extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MedicalCondition>
     */
    public static $model = \App\Models\MedicalCondition::class;
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
            BelongsTo::make('Patient')
                ->searchable()
                ->required(),

            BelongsTo::make('Encounter')
                ->nullable(),

            ID::make()->sortable(),

            Text::make('Condition Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Date::make('Diagnosis Date')
                ->sortable()
                ->rules('required', 'date'),

            Select::make('Status')
                ->options([
                    'active' => 'Active',
                    'resolved' => 'Resolved',
                    'chronic' => 'Chronic',
                    'in_treatment' => 'In Treatment',
                ])
                ->sortable()
                ->rules('required'),

            Textarea::make('Treatment Plan')
                ->rows(3)
                ->alwaysShow(),

            Textarea::make('Notes')
                ->rows(3)
                ->alwaysShow(),

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
