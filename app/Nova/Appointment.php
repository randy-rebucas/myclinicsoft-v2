<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Filters;

class Appointment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Appointment>
     */
    public static $model = \App\Models\Appointment::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Scheduling';

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

            BelongsTo::make('Clinic')
                ->rules('required')
                ->searchable(),

            Date::make('Appointment Date')
                ->sortable()
                ->rules('required'),

            DateTime::make('Appointment Time')
                ->rules('required'),

            Number::make('Duration (minutes)')
                ->default(30)
                ->rules('required', 'integer', 'min:15', 'max:240'),

            Select::make('Type')->options([
                'consultation' => 'Consultation',
                'follow_up' => 'Follow-up',
                'emergency' => 'Emergency',
                'routine' => 'Routine Check-up',
            ])->rules('required'),

            Select::make('Status')->options([
                'scheduled' => 'Scheduled',
                'confirmed' => 'Confirmed',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'no_show' => 'No Show',
            ])->rules('required'),

            Textarea::make('Notes')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make('Cancellation Reason')
                ->nullable()
                ->hideFromIndex()
                ->hideWhenCreating(),
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
        return [
            new Filters\AppointmentStatusFilter,
            new Filters\DateRangeFilter,
        ];
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
