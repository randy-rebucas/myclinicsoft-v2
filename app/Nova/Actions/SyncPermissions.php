<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use App\Models\Permission;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Http\Requests\NovaRequest;

class SyncPermissions extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Sync Permissions');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->permissions()->sync($fields->permissions);
        }

        return Action::message('Permissions synced successfully!');
    }

    public function fields(NovaRequest $request)
    {
        return [
            MultiSelect::make('Permissions')
                ->options(Permission::all()->pluck('name', 'id'))
                ->filterable(),
        ];
    }
}
