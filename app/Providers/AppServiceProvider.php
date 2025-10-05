<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Doctor;
use App\Models\Setting;
use App\Models\Patient;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Encounter;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Queue;
use App\Models\Medication;
use App\Models\Allergy;
use App\Models\Vital;
use App\Observers\DoctorObserver;
use App\Observers\PatientObserver;
use App\Observers\UserObserver;
use App\Observers\ClinicObserver;
use App\Observers\EncounterObserver;
use App\Observers\AppointmentObserver;
use App\Observers\PrescriptionObserver;
use App\Observers\QueueObserver;
use App\Observers\MedicationObserver;
use App\Observers\AllergyObserver;
use App\Observers\VitalObserver;
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

        // Register all model observers
        User::observe(UserObserver::class);
        Patient::observe(PatientObserver::class);
        Doctor::observe(DoctorObserver::class);
        Clinic::observe(ClinicObserver::class);
        Encounter::observe(EncounterObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Prescription::observe(PrescriptionObserver::class);
        Queue::observe(QueueObserver::class);
        Medication::observe(MedicationObserver::class);
        Allergy::observe(AllergyObserver::class);
        Vital::observe(VitalObserver::class);
    }
}
