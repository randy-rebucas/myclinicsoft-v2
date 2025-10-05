<?php

namespace App\Providers;

use App\Services\PrescriptionInvoice;
use Illuminate\Foundation\Application;
use LaravelDaily\Invoices\InvoiceServiceProvider as BaseServiceProvider;
class ClinicPrescriptionProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function registerServices()
    {
        $this->app->singleton(PrescriptionInvoice::class, function (Application $app) {
            return new PrescriptionInvoice(config('prescription-invoice.patient.attributes.name'));
        });
    }
}
