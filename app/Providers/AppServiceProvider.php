<?php

namespace App\Providers;

use App\Models\MedRepresentative;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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
        // Only load settings if database is connected and table exists
        try {
            if (Schema::hasTable('settings')) {
                if (!config('settings')) {
                    foreach (Setting::all()->pluck('value', 'key') as $k => $val) {
                        config(['settings.' . $k => $val]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the application boot
            Log::warning('Failed to load settings: ' . $e->getMessage());
        }

        // Implicitly grant "admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user && $user->hasRole('admin')) {
                return true;
            }
        });

        Patient::observe(PatientObserver::class);
        Doctor::observe(DoctorObserver::class);
        MedRepresentative::observe(MedRepresentativeObserver::class);
        Receptionist::observe(ReceptionistObserver::class);
    }
}
