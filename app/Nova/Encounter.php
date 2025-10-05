<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class Encounter extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Encounter>
     */
    public static $model = \App\Models\Encounter::class;

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
            ID::make()->sortable(),

            BelongsTo::make('Patient')
                ->rules('required')
                ->searchable(),

            BelongsTo::make('Doctor')
                ->rules('required')
                ->searchable(),

            Textarea::make('Chief Complaint')
                ->rules('required')
                ->alwaysShow(),

            Date::make('Encounter Date')
                ->sortable()
                ->rules('required'),

            DateTime::make('Encounter Time')
                ->rules('required'),

            Select::make('Appointment Type')->options([
                'consultation' => 'Consultation',
                'follow_up' => 'Follow-up',
                'emergency' => 'Emergency',
                'routine' => 'Routine Check-up',
            ])->rules('required'),

            Number::make('Duration (minutes)')
                ->default(30)
                ->rules('required', 'integer', 'min:15', 'max:240'),

            Text::make('Diagnosis')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make('Treatment Plan')
                ->nullable()
                ->hideFromIndex(),

            Date::make('Follow Up Date')
                ->nullable()
                ->hideFromIndex(),

            Select::make('Status')->options([
                'scheduled' => 'Scheduled',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ])->rules('required'),

            Textarea::make('Notes')
                ->nullable()
                ->alwaysShow(),

            HasMany::make('Medications'),
            HasMany::make('Prescriptions'),
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
