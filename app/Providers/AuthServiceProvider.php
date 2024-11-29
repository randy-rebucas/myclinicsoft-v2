<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Patient;
use App\Models\Doctor;
use App\Policies\PatientPolicy;
use App\Policies\DoctorPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Patient::class => PatientPolicy::class,
        Doctor::class => DoctorPolicy::class,
        // Add other policies
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define gates for general permissions
        Gate::define('manage-system', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-appointments', function ($user) {
            return in_array($user->role, ['doctor', 'receptionist', 'admin']);
        });

        Gate::define('view-medical-records', function ($user) {
            return in_array($user->role, ['doctor', 'admin']);
        });

        Gate::define('manage-prescriptions', function ($user) {
            return $user->role === 'doctor';
        });

        Gate::define('access-med-inventory', function ($user) {
            return in_array($user->role, ['med_representative', 'admin']);
        });
    }
}
