<?php

namespace App\Providers;

use App\Nova\Allergy;
use App\Nova\Dashboards\Main;
use App\Nova\Encounter;
use App\Nova\Medication;
use App\Nova\Patient;
use App\Nova\Setting;
use App\Nova\User;
use App\Nova\Vital;
use App\Nova\Doctor;
use App\Nova\Queue;
use App\Nova\Clinic;
use App\Nova\Appointment;
use App\Nova\Prescription;
use App\Nova\Address;
use App\Nova\Activity;
use App\Nova\AuditLog;
use App\Nova\Notification;
use App\Nova\Role;
use App\Nova\Permission;
use App\Nova\PatientDoctor;
use App\Nova\ClinicDoctor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Nova;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::make('Users', [
                    MenuItem::resource(Doctor::class),
                    MenuItem::resource(Patient::class),
                    MenuItem::resource(User::class),
                ])->icon('users')->collapsable(),

                MenuSection::make('Medical Records', [
                    MenuItem::resource(Encounter::class),
                    MenuItem::resource(Vital::class),
                    MenuItem::resource(Medication::class),
                    MenuItem::resource(Prescription::class),
                    MenuItem::resource(Appointment::class),
                ])->icon('document-text')->collapsable(),

                MenuSection::make('Patient History', [
                    MenuItem::resource(Allergy::class),
                ])->icon('clipboard-list')->collapsable(),

                MenuSection::make('Clinic Management', [
                    MenuItem::resource(Queue::class),
                    MenuItem::resource(Clinic::class),
                    MenuItem::resource(Setting::class),
                ])->icon('building-office')->collapsable(),

                MenuSection::make('System', [
                    MenuItem::resource(Role::class),
                    MenuItem::resource(Permission::class),
                    MenuItem::resource(Activity::class),
                    MenuItem::resource(AuditLog::class),
                    MenuItem::resource(Notification::class),
                ])->icon('cog')->collapsable(),

                MenuSection::make('Relationships', [
                    MenuItem::resource(PatientDoctor::class),
                    MenuItem::resource(ClinicDoctor::class),
                ])->icon('link')->collapsable(),
            ];
        });

        Nova::footer(function ($request) {
            return Blade::render('
                @env(\'prod\')
                    This is production!
                @endenv
            ');
        });

        Nova::withBreadcrumbs();
        Nova::withoutThemeSwitcher();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return true;
            // return in_array($user->email, [
            //     'admin@kidzklinka.com'
            // ]);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Nova::report(function ($exception) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }
        });
    }
}
