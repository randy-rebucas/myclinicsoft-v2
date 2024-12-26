<?php

use App\Http\Controllers\DatabaseDumper;
use App\Http\Controllers\Prescription;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth'])->group(function () {

    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/profile', 'user.profile')->name('profile');
    Volt::route('/queue-display', 'queue.display')->name('queue-display');

    Route::middleware(['role:doctor'])->group(function () {
        Volt::route('/patients', 'patient.index')->name('patients');
        Volt::route('/patient/record/{queueId}', 'patient.record.index')->name('patient-record');
        Volt::route('/med-representatives', 'med-representative.index')->name('med-representatives');
        Volt::route('/receptionists', 'receptionist.index')->name('receptionists');
        Volt::route('/queue', 'queue.index')->name('queue');
        Volt::route('/roles', 'role.index')->name('roles');
        Volt::route('/settings', 'setting.index')->name('settings');

        Route::get('/patient/medication/{medicationId}', Prescription::class)->name('prescription');
    });

    Route::get('/dump', DatabaseDumper::class)->name('dump');
});

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome');

});

require __DIR__ . '/auth.php';
