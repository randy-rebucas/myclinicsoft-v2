<?php

namespace App\Nova;

use App\Nova\Repeater\MedicationItem;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Repeater;
use Laravel\Nova\Actions\Action;

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
            ID::make()->hideFromDetail(),

            BelongsTo::make('Patient')
                ->searchable()
                ->required(),

            BelongsTo::make('Encounter')
                ->sortable()
                ->nullable(),


            Repeater::make('Medication Items', 'prescription_items')
                ->uniqueField('id')
                ->repeatables([
                    \App\Nova\Repeater\MedicationItem::make()->confirmRemoval(),
                ])->asJson(),

            Textarea::make('Notes')
                ->alwaysShow()
                ->nullable(),

        ];
    }
    /**
     * Get the fields displayed by the resource on detail page.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fieldsForDetail(NovaRequest $request)
    {
        return [
            BelongsTo::make('Patient')
                ->searchable()
                ->required(),

            Text::make('Prescription Items', function () {
                return view('partials.medication-items', [
                    'items' => $this->prescription_items,
                ])->render();
            })->asHtml(),

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

    public function actions(NovaRequest $request)
    {
        return [
            Actions\PrintPrescriptionPDF::make()
                ->onlyOnDetail()
                ->confirmButtonText('Generate PDF')
                ->cancelButtonText('Cancel'),
        ];
    }
}
