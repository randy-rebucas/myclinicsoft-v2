<?php

namespace App\Providers;

use App\Nova\Allergy;
use App\Nova\Dashboards\Main;
use App\Nova\Department;
use App\Nova\DiagnosticTest;
use App\Nova\Encounter;
use App\Nova\FamilyHistory;
use App\Nova\Immunization;
use App\Nova\MedicalCondition;
use App\Nova\Medication;
use App\Nova\MedRepresentative;
use App\Nova\Patient;
use App\Nova\Receptionist;
use App\Nova\Setting;
use App\Nova\User;
use App\Nova\Vital;
use App\Nova\Ads;
use App\Nova\Doctor;
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
                    MenuItem::resource(Receptionist::class),
                    MenuItem::resource(MedRepresentative::class),
                    MenuItem::resource(User::class),
                ])->icon('users')->collapsable(),

                MenuSection::make('Medical Records', [
                    MenuItem::resource(Encounter::class),
                    MenuItem::resource(Vital::class),
                    MenuItem::resource(DiagnosticTest::class),
                    MenuItem::resource(Medication::class),
                    MenuItem::resource(Immunization::class),
                ])->icon('document-text')->collapsable(),

                MenuSection::make('Patient History', [
                    MenuItem::resource(Allergy::class),
                    MenuItem::resource(MedicalCondition::class),
                    MenuItem::resource(FamilyHistory::class),
                ])->icon('clipboard-list')->collapsable(),

                MenuSection::make('Settings', [
                    MenuItem::resource(Department::class),
                    MenuItem::resource(Setting::class),
                    MenuItem::resource(Ads::class),
                ])->icon('cog')->collapsable(),
            ];
        });

        Nova::userMenu(function (Request $request, Menu $menu) {
            if ($request->user()->doctor) {
                $menu->append(
                    MenuItem::make('My Profile')
                        ->path("/resources/doctors/{$request->user()->doctor->id}")
                );

                if ($request->user()->doctor->subscribed()) {
                    $menu->append(
                        MenuItem::make('Subscriber Dashboard')
                            ->path('/subscribers/dashboard')
                    );
                }
            }

            return $menu;
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
            return in_array($user->email, [
                'admin@example.com'
            ]);
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
