<?php

use App\Http\Controllers\DatabaseDumper;
use App\Http\Controllers\Prescription;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\WebhookController;

Route::middleware(['auth', 'check.subscription'])->group(function () {

    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/profile', 'user.profile')->name('profile');

    Route::middleware(['role:doctor'])->group(function () {
        Volt::route('/patients', 'patient.index')->name('patients');
        Volt::route('/med-representatives', 'med-representative.index')->name('med-representatives');
        Volt::route('/receptionists', 'receptionist.index')->name('receptionists');
        Volt::route('/queue', 'queue.index')->name('queue');
        Volt::route('/roles', 'role.index')->name('roles');
        Volt::route('/settings', 'setting.index')->name('settings');

        Route::get('/patient/encounter/{encounterId}', Prescription::class)->name('prescription');
    });

    Route::get('/dump', DatabaseDumper::class)->name('dump');

    // // Doctor routes
    // Route::middleware(['can:manage-prescriptions'])->group(function () {
    //     Route::resource('prescriptions', PrescriptionController::class);
    // });

    // // Med Representative routes
    // Route::middleware(['can:access-med-inventory'])->group(function () {
    //     Route::resource('inventory', InventoryController::class);
    // });
});

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome');
    Volt::route('/queue-display', 'queue.display')->name('queue-display');
    Route::post('/webhook/stripe', [WebhookController::class, 'handleStripeWebhook']);
});

// Add this new route group for billing-related routes
Route::middleware(['auth'])->group(function () {
    Volt::route('/billing', 'subscription.billing')->name('billing');
});

require __DIR__ . '/auth.php';
