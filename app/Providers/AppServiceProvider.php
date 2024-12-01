<?php

namespace App\Providers;

use App\Models\MedRepresentative;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Doctor;
use App\Models\Setting;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Observers\DoctorObserver;
use App\Observers\MedRepresentativeObserver;
use App\Observers\PatientObserver;
use App\Observers\ReceptionistObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            if (!Config('settings')) {
                foreach (Setting::all()->pluck('value', 'key') as $k => $val) {
                    config(['settings.' . $k => $val]);
                }
            }
        }

        // Implicitly grant "admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        Patient::observe(PatientObserver::class);
        Doctor::observe(DoctorObserver::class);
        MedRepresentative::observe(MedRepresentativeObserver::class);
        Receptionist::observe(ReceptionistObserver::class);
    }
}
