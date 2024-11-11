<?php

use App\Http\Controllers\DatabaseDumper;
use App\Http\Controllers\Prescription;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome');

    Volt::route('/setup', 'setup.index')->name('setup');
});

Route::middleware(['auth'])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/profile', 'user.profile')->name('profile');

    Volt::route('/patients', 'patient.index')->name('patients');
    Volt::route('/patient/detail', 'patient.detail')->name('patient-detail');
    
    Volt::route('/doctors', 'doctor.index')->name('doctors');
    Volt::route('/med-representatives', 'med-representative.index')->name('med-representatives');
    Volt::route('/receptionists', 'receptionist.index')->name('receptionists');
    
    Volt::route('/roles', 'role.index')->name('roles');
    Volt::route('/settings', 'setting.index')->name('settings');
    
    Route::get('/patient/encounter/{encounterId}', Prescription::class)->name('prescription');
    Route::get('/patient/{patient}/prescriptions', App\Livewire\Patient\Prescription\Index::class)->name('patient.prescriptions.index');
});

Route::get('/dump', DatabaseDumper::class)->name('dump');

require __DIR__.'/auth.php';
