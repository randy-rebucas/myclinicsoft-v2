<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Role;
use Laravel\Nova\Fields\MultiSelect;

class UpdateUserRoles extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Update Roles');
    }

    public function handle(ActionFields $fields, $models)
    {
        $model = $models->first();
        $model->assignRole($fields->role);

        return Action::message('Roles updated successfully!');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Select::make('Role', 'roles')
                ->options(Role::pluck('name', 'id'))
                ->rules('required'),
        ];
    }
}
