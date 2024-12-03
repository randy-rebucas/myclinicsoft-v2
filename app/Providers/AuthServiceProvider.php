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
    }
}
