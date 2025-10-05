<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EncounterController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    });

    // Authenticated API routes
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // User routes
        Route::prefix('user')->group(function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::post('/change-password', [UserController::class, 'changePassword']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // Patient routes
        Route::prefix('patients')->middleware('permission:view patients')->group(function () {
            Route::get('/', [PatientController::class, 'index']);
            Route::post('/', [PatientController::class, 'store'])->middleware('permission:create patients');
            Route::get('/{patient}', [PatientController::class, 'show']);
            Route::put('/{patient}', [PatientController::class, 'update'])->middleware('permission:update patients');
            Route::delete('/{patient}', [PatientController::class, 'destroy'])->middleware('permission:delete patients');
            Route::get('/{patient}/records', [PatientController::class, 'records'])->middleware('permission:view patient records');
            Route::get('/{patient}/appointments', [PatientController::class, 'appointments'])->middleware('permission:manage appointments');
            Route::get('/{patient}/prescriptions', [PatientController::class, 'prescriptions'])->middleware('permission:view prescriptions');
        });

        // Doctor routes
        Route::prefix('doctors')->middleware('permission:view doctors')->group(function () {
            Route::get('/', [DoctorController::class, 'index']);
            Route::post('/', [DoctorController::class, 'store'])->middleware('permission:create doctors');
            Route::get('/{doctor}', [DoctorController::class, 'show']);
            Route::put('/{doctor}', [DoctorController::class, 'update'])->middleware('permission:update doctors');
            Route::delete('/{doctor}', [DoctorController::class, 'destroy'])->middleware('permission:delete doctors');
            Route::get('/{doctor}/appointments', [DoctorController::class, 'appointments'])->middleware('permission:manage appointments');
            Route::get('/{doctor}/patients', [DoctorController::class, 'patients'])->middleware('permission:view patients');
        });

        // Clinic routes
        Route::prefix('clinics')->middleware('permission:view clinics')->group(function () {
            Route::get('/', [ClinicController::class, 'index']);
            Route::post('/', [ClinicController::class, 'store'])->middleware('permission:create clinics');
            Route::get('/{clinic}', [ClinicController::class, 'show']);
            Route::put('/{clinic}', [ClinicController::class, 'update'])->middleware('permission:update clinics');
            Route::delete('/{clinic}', [ClinicController::class, 'destroy'])->middleware('permission:delete clinics');
            Route::get('/{clinic}/doctors', [ClinicController::class, 'doctors'])->middleware('permission:view doctors');
            Route::get('/{clinic}/appointments', [ClinicController::class, 'appointments'])->middleware('permission:manage appointments');
        });

        // Queue routes
        Route::prefix('queue')->middleware('permission:view queue')->group(function () {
            Route::get('/', [QueueController::class, 'index']);
            Route::post('/', [QueueController::class, 'store'])->middleware('permission:manage queue');
            Route::get('/{queue}', [QueueController::class, 'show']);
            Route::put('/{queue}', [QueueController::class, 'update'])->middleware('permission:manage queue');
            Route::delete('/{queue}', [QueueController::class, 'destroy'])->middleware('permission:manage queue');
            Route::post('/{queue}/call', [QueueController::class, 'call'])->middleware('permission:update queue status');
            Route::post('/{queue}/complete', [QueueController::class, 'complete'])->middleware('permission:update queue status');
            Route::post('/{queue}/cancel', [QueueController::class, 'cancel'])->middleware('permission:manage queue');
            Route::get('/status/{queue}', [QueueController::class, 'status']);
        });

        // Appointment routes
        Route::prefix('appointments')->middleware('permission:manage appointments')->group(function () {
            Route::get('/', [AppointmentController::class, 'index']);
            Route::post('/', [AppointmentController::class, 'store']);
            Route::get('/{appointment}', [AppointmentController::class, 'show']);
            Route::put('/{appointment}', [AppointmentController::class, 'update']);
            Route::delete('/{appointment}', [AppointmentController::class, 'destroy']);
            Route::post('/{appointment}/confirm', [AppointmentController::class, 'confirm']);
            Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel']);
            Route::post('/{appointment}/complete', [AppointmentController::class, 'complete']);
            Route::get('/{appointment}/available-slots', [AppointmentController::class, 'availableSlots']);
        });

        // Encounter routes
        Route::prefix('encounters')->middleware('permission:view encounters')->group(function () {
            Route::get('/', [EncounterController::class, 'index']);
            Route::post('/', [EncounterController::class, 'store'])->middleware('permission:create encounters');
            Route::get('/{encounter}', [EncounterController::class, 'show']);
            Route::put('/{encounter}', [EncounterController::class, 'update'])->middleware('permission:update encounters');
            Route::delete('/{encounter}', [EncounterController::class, 'destroy'])->middleware('permission:delete encounters');
            Route::post('/{encounter}/start', [EncounterController::class, 'start'])->middleware('permission:update encounters');
            Route::post('/{encounter}/complete', [EncounterController::class, 'complete'])->middleware('permission:update encounters');
        });

        // Prescription routes
        Route::prefix('prescriptions')->middleware('permission:view prescriptions')->group(function () {
            Route::get('/', [PrescriptionController::class, 'index']);
            Route::post('/', [PrescriptionController::class, 'store'])->middleware('permission:create prescriptions');
            Route::get('/{prescription}', [PrescriptionController::class, 'show']);
            Route::put('/{prescription}', [PrescriptionController::class, 'update'])->middleware('permission:update prescriptions');
            Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->middleware('permission:delete prescriptions');
            Route::get('/{prescription}/print', [PrescriptionController::class, 'print'])->middleware('permission:print prescriptions');
            Route::post('/{prescription}/ready', [PrescriptionController::class, 'markReady'])->middleware('permission:update prescriptions');
        });

        // Report routes
        Route::prefix('reports')->group(function () {
            Route::get('/patients', [ReportController::class, 'patients'])->middleware('permission:view patients');
            Route::get('/appointments', [ReportController::class, 'appointments'])->middleware('permission:manage appointments');
            Route::get('/prescriptions', [ReportController::class, 'prescriptions'])->middleware('permission:view prescriptions');
            Route::get('/queue', [ReportController::class, 'queue'])->middleware('permission:view queue');
            Route::get('/dashboard', [ReportController::class, 'dashboard'])->middleware('permission:view dashboard');
        });

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::prefix('admin')->group(function () {
                Route::get('/users', [UserController::class, 'index'])->middleware('permission:view users');
                Route::post('/users', [UserController::class, 'store'])->middleware('permission:create users');
                Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:view users');
                Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:update users');
                Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete users');
                Route::get('/activities', [UserController::class, 'activities'])->middleware('permission:view activities');
                Route::get('/audit-logs', [UserController::class, 'auditLogs'])->middleware('permission:view audit logs');
            });
        });
    });
});
