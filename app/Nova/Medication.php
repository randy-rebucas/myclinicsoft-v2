<?php

namespace App\Nova;

use App\Nova\Repeater\PrescriptionItem;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Repeater;

class Medication extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Medication>
     */
    public static $model = \App\Models\Medication::class;
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
                ->sortable()
                ->nullable(),

            ID::make()->hideFromIndex()->hideFromDetail(),

            Repeater::make('Prescriptions')
                ->repeatables([
                    PrescriptionItem::make(),
                ]),

            Textarea::make('Notes')
                ->alwaysShow()
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
