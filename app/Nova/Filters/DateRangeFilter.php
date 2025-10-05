<?php

namespace App\Nova\Filters;

use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\DateFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class DateRangeFilter extends DateFilter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'date-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        $date = Carbon::parse($value);
        
        return $query->whereBetween($this->getDateColumn(), [
            $date->startOfDay(),
            $date->endOfDay()
        ]);
    }

    /**
     * Get the date column to filter by.
     *
     * @return string
     */
    protected function getDateColumn()
    {
        return 'created_at';
    }
}
