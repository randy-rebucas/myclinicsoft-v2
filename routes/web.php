<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome');
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
});


require __DIR__.'/auth.php';
