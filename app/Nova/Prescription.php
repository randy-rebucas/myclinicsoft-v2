<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Prescription extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Prescription>
     */
    public static $model = \App\Models\Prescription::class;

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
        'medication_name',
        'patient.first_name',
        'patient.last_name',
        'doctor.first_name',
        'doctor.last_name',
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

            BelongsTo::make('Patient')
                ->rules('required')
                ->searchable(),

            BelongsTo::make('Doctor')
                ->rules('required')
                ->searchable(),

            BelongsTo::make('Encounter')
                ->nullable()
                ->searchable(),

            Text::make('Medication Name')
                ->sortable()
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
                ->nullable()
                ->hideFromIndex(),

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
