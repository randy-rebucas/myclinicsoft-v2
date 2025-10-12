<?php

use App\Http\Controllers\DatabaseDumper;
use App\Http\Controllers\Prescription;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public routes
Route::middleware('guest')->group(function () {
    Route::view('/', 'welcome')->name('welcome');
});

// Public setup route (no auth required)
Volt::route('/setup', 'setup.index')->name('setup');

// Authenticated routes
Route::middleware(['check.initial.user', 'auth', 'verified'])->group(function () {
    
    // Dashboard - accessible to all authenticated users
    Volt::route('/dashboard', 'dashboard')
        ->middleware('permission:view dashboard')
        ->name('dashboard');
    
    // User profile - accessible to all authenticated users
    Volt::route('/profile', 'user.profile')->name('profile');

    // Queue display - accessible to all authenticated users
    Volt::route('/queue-display', 'queue.display')->name('queue-display');

    // Patient routes
    Route::prefix('patients')->name('patients.')->group(function () {
        Volt::route('/', 'patient.index')
            ->middleware('permission:view patients')
            ->name('index');
        
        Volt::route('/create', 'patient.form')
            ->middleware('permission:create patients')
            ->name('create');
        
        Volt::route('/{patientId}', 'patient.detail')
            ->middleware('permission:view patients')
            ->name('show');
        
        Volt::route('/{patientId}/edit', 'patient.form')
            ->middleware('permission:update patients')
            ->name('edit');
        
        Volt::route('/{patientId}/record', 'patient.record.index')
            ->middleware('permission:view patient records')
            ->name('record');
        
        Volt::route('/{patientId}/record/{queueId}', 'patient.record.index')
            ->middleware('permission:view patient records')
            ->name('record.queue');
    });

    // Doctor routes
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Volt::route('/', 'doctor.index')
            ->middleware('permission:view doctors')
            ->name('index');
        
        Volt::route('/create', 'doctor.form')
            ->middleware('permission:create doctors')
            ->name('create');
        
        Volt::route('/{doctor}', 'doctor.detail')
            ->middleware('permission:view doctors')
            ->name('show');
        
        Volt::route('/{doctor}/edit', 'doctor.form')
            ->middleware('permission:update doctors')
            ->name('edit');
    });

    // Clinic routes
    Route::prefix('clinics')->name('clinics.')->group(function () {
        Volt::route('/', 'clinic.index')
            ->middleware('permission:view clinics')
            ->name('index');
        
        Volt::route('/create', 'clinic.form')
            ->middleware('permission:create clinics')
            ->name('create');
        
        Volt::route('/{clinic}', 'clinic.detail')
            ->middleware('permission:view clinics')
            ->name('show');
        
        Volt::route('/{clinic}/edit', 'clinic.form')
            ->middleware('permission:update clinics')
            ->name('edit');
    });

    // Queue routes
    Route::prefix('queue')->name('queue.')->group(function () {
        Volt::route('/', 'queue.index')
            ->middleware('permission:view queue')
            ->name('index');
        
        Volt::route('/create', 'queue.form')
            ->middleware('permission:manage queue')
            ->name('create');
        
        Route::post('/{queue}/call', [QueueController::class, 'call'])
            ->middleware('permission:update queue status')
            ->name('call');
        
        Route::post('/{queue}/complete', [QueueController::class, 'complete'])
            ->middleware('permission:update queue status')
            ->name('complete');
        
        Route::post('/{queue}/cancel', [QueueController::class, 'cancel'])
            ->middleware('permission:manage queue')
            ->name('cancel');
        
        Route::get('/status/{queue}', [QueueController::class, 'status'])
            ->middleware('permission:view queue')
            ->name('status');
    });

    // Appointment routes
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Volt::route('/', 'appointment.index')
            ->middleware('permission:manage appointments')
            ->name('index');
        
        Volt::route('/create', 'appointment.form')
            ->middleware('permission:manage appointments')
            ->name('create');
        
        Volt::route('/{appointment}', 'appointment.detail')
            ->middleware('permission:manage appointments')
            ->name('show');
        
        Volt::route('/{appointment}/edit', 'appointment.form')
            ->middleware('permission:manage appointments')
            ->name('edit');
        
        Route::post('/{appointment}/confirm', [AppointmentController::class, 'confirm'])
            ->middleware('permission:manage appointments')
            ->name('confirm');
        
        Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->middleware('permission:manage appointments')
            ->name('cancel');
        
        Route::post('/{appointment}/complete', [AppointmentController::class, 'complete'])
            ->middleware('permission:manage appointments')
            ->name('complete');
    });

    // Encounter routes
    Route::prefix('encounters')->name('encounters.')->group(function () {
        Volt::route('/', 'encounter.index')
            ->middleware('permission:view encounters')
            ->name('index');
        
        Volt::route('/create', 'encounter.form')
            ->middleware('permission:create encounters')
            ->name('create');
        
        Volt::route('/{encounter}', 'encounter.detail')
            ->middleware('permission:view encounters')
            ->name('show');
        
        Volt::route('/{encounter}/edit', 'encounter.form')
            ->middleware('permission:update encounters')
            ->name('edit');
        
        Route::post('/{encounter}/start', [EncounterController::class, 'start'])
            ->middleware('permission:update encounters')
            ->name('start');
        
        Route::post('/{encounter}/complete', [EncounterController::class, 'complete'])
            ->middleware('permission:update encounters')
            ->name('complete');
    });

    // Prescription routes
    Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
        Volt::route('/', 'prescription.index')
            ->middleware('permission:view prescriptions')
            ->name('index');
        
        Volt::route('/create', 'prescription.form')
            ->middleware('permission:create prescriptions')
            ->name('create');
        
        Volt::route('/{prescription}', 'prescription.detail')
            ->middleware('permission:view prescriptions')
            ->name('show');
        
        Volt::route('/{prescription}/edit', 'prescription.form')
            ->middleware('permission:update prescriptions')
            ->name('edit');
        
        Route::get('/{prescription}/print', [PrescriptionController::class, 'print'])
            ->middleware('permission:print prescriptions')
            ->name('print');
        
        Route::get('/{prescription}/download', [PrescriptionController::class, 'download'])
            ->middleware('permission:print prescriptions')
            ->name('download');
        
        Route::get('/{prescription}/pdf-data', [PrescriptionController::class, 'getPdfData'])
            ->middleware('permission:print prescriptions')
            ->name('pdf-data');
        
        Route::post('/{prescription}/ready', [PrescriptionController::class, 'markReady'])
            ->middleware('permission:update prescriptions')
            ->name('ready');
    });

    // Medication routes
    Route::prefix('medications')->name('medications.')->group(function () {
        Volt::route('/', 'medication.index')
            ->middleware('permission:view medications')
            ->name('index');
        
        Volt::route('/create', 'medication.form')
            ->middleware('permission:create medications')
            ->name('create');
        
        Volt::route('/{medication}', 'medication.detail')
            ->middleware('permission:view medications')
            ->name('show');
        
        Volt::route('/{medication}/edit', 'medication.form')
            ->middleware('permission:update medications')
            ->name('edit');
    });

    // Allergy routes
    Route::prefix('allergies')->name('allergies.')->group(function () {
        Volt::route('/', 'allergy.index')
            ->middleware('permission:view patient records')
            ->name('index');
        
        Volt::route('/create', 'allergy.form')
            ->middleware('permission:create patient records')
            ->name('create');
        
        Volt::route('/{allergy}/edit', 'allergy.form')
            ->middleware('permission:update patient records')
            ->name('edit');
    });

    // Vital routes
    Route::prefix('vitals')->name('vitals.')->group(function () {
        Volt::route('/', 'vital.index')
            ->middleware('permission:view patient records')
            ->name('index');
        
        Volt::route('/create', 'vital.form')
            ->middleware('permission:create patient records')
            ->name('create');
        
        Volt::route('/{vital}/edit', 'vital.form')
            ->middleware('permission:update patient records')
            ->name('edit');
    });

    // User management routes (Admin only)
    Route::prefix('users')->name('users.')->middleware('role:admin')->group(function () {
        Volt::route('/', 'user.index')
            ->middleware('permission:view users')
            ->name('index');
        
        Volt::route('/create', 'user.form')
            ->middleware('permission:create users')
            ->name('create');
        
        Volt::route('/{user}', 'user.detail')
            ->middleware('permission:view users')
            ->name('show');
        
        Volt::route('/{user}/edit', 'user.form')
            ->middleware('permission:update users')
            ->name('edit');
    });

    // Role and permission management routes (Admin only)
    Route::prefix('roles')->name('roles.')->middleware('role:admin')->group(function () {
        Volt::route('/', 'role.index')
            ->middleware('permission:view roles')
            ->name('index');
        
        Volt::route('/create', 'role.form')
            ->middleware('permission:create roles')
            ->name('create');
        
        Volt::route('/{role}/edit', 'role.form')
            ->middleware('permission:update roles')
            ->name('edit');
        
        Volt::route('/permissions', 'role.permission.index')
            ->middleware('permission:view permissions')
            ->name('permissions');
    });

    // User Settings routes - users can only access their own settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Volt::route('/', 'setting.index')
            ->middleware('auth')
            ->name('index');
        
        Volt::route('/professional', 'setting.form.professional')
            ->middleware('role:doctor')
            ->name('professional');
        
        Volt::route('/clinics', 'setting.form.clinic-associations')
            ->middleware('role:doctor|admin')
            ->name('clinics');
        
        Volt::route('/system', 'setting.form.system')
            ->middleware('role:admin')
            ->name('system');
    });

    // Reports routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Volt::route('/', 'report.index')
            ->middleware('permission:view dashboard')
            ->name('index');
        
        Route::get('/patients', [ReportController::class, 'patients'])
            ->middleware('permission:view patients')
            ->name('patients');
        
        Route::get('/appointments', [ReportController::class, 'appointments'])
            ->middleware('permission:manage appointments')
            ->name('appointments');
        
        Route::get('/prescriptions', [ReportController::class, 'prescriptions'])
            ->middleware('permission:view prescriptions')
            ->name('prescriptions');
        
        Route::get('/queue', [ReportController::class, 'queue'])
            ->middleware('permission:view queue')
            ->name('queue');
    });

    // Activity and audit routes
    Route::prefix('activities')->name('activities.')->group(function () {
        Volt::route('/', 'activity.index')
            ->middleware('permission:view activities')
            ->name('index');
    });

    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Volt::route('/', 'audit-log.index')
            ->middleware('permission:view audit logs')
            ->name('index');
    });

    // System administration routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/dump', DatabaseDumper::class)
            ->middleware('permission:dump database')
            ->name('dump');
    });

    // Legacy prescription route (for backward compatibility)
    Route::get('/patient/medication/{medicationId}', Prescription::class)
        ->middleware('permission:print prescriptions')
        ->name('prescription.legacy');
});

require __DIR__ . '/auth.php';
