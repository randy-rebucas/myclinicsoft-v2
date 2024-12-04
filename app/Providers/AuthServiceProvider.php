<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Patient;
use App\Models\Doctor;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Add other policies
    ];

    public function boot()
    {
        $this->registerPolicies();
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }

}
