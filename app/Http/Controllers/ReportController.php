<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get patients report
     */
    public function patients(Request $request): JsonResponse
    {
        $query = Patient::with(['user', 'encounters', 'appointments']);

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->has('clinic_id')) {
            $query->whereHas('appointments', function ($q) use ($request) {
                $q->where('clinic_id', $request->clinic_id);
            });
        }

        $patients = $query->paginate($request->get('per_page', 15));

        // Get summary statistics
        $summary = [
            'total_patients' => Patient::count(),
            'new_patients_this_month' => Patient::whereMonth('created_at', now()->month)->count(),
            'patients_with_appointments' => Patient::whereHas('appointments')->count(),
            'patients_with_prescriptions' => Patient::whereHas('prescriptions')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'patients' => $patients,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get appointments report
     */
    public function appointments(Request $request): JsonResponse
    {
        $query = Appointment::with(['patient', 'doctor', 'clinic']);

        if ($request->has('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->paginate($request->get('per_page', 15));

        // Get summary statistics
        $summary = [
            'total_appointments' => Appointment::count(),
            'scheduled_appointments' => Appointment::where('status', 'scheduled')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'appointments_this_month' => Appointment::whereMonth('appointment_date', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'appointments' => $appointments,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get prescriptions report
     */
    public function prescriptions(Request $request): JsonResponse
    {
        $query = Prescription::with(['patient', 'doctor', 'encounter']);

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        // Get summary statistics
        $summary = [
            'total_prescriptions' => Prescription::count(),
            'active_prescriptions' => Prescription::where('status', 'active')->count(),
            'completed_prescriptions' => Prescription::where('status', 'completed')->count(),
            'prescriptions_this_month' => Prescription::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'prescriptions' => $prescriptions,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get queue report
     */
    public function queue(Request $request): JsonResponse
    {
        $query = Queue::with(['patient', 'doctor', 'clinic']);

        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $queues = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        // Get summary statistics
        $summary = [
            'total_queue_entries' => Queue::count(),
            'waiting_patients' => Queue::where('status', 'waiting')->count(),
            'completed_queues' => Queue::where('status', 'completed')->count(),
            'cancelled_queues' => Queue::where('status', 'cancelled')->count(),
            'queue_entries_today' => Queue::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'queues' => $queues,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'patients' => [
                'total' => Patient::count(),
                'new_this_month' => Patient::whereMonth('created_at', now()->month)->count(),
                'new_today' => Patient::whereDate('created_at', today())->count(),
            ],
            'appointments' => [
                'total' => Appointment::count(),
                'scheduled' => Appointment::where('status', 'scheduled')->count(),
                'completed_today' => Appointment::where('status', 'completed')
                    ->whereDate('appointment_date', today())->count(),
                'upcoming_today' => Appointment::where('status', 'scheduled')
                    ->whereDate('appointment_date', today())->count(),
            ],
            'prescriptions' => [
                'total' => Prescription::count(),
                'active' => Prescription::where('status', 'active')->count(),
                'ready_for_pickup' => Prescription::where('status', 'ready')->count(),
            ],
            'queue' => [
                'waiting' => Queue::where('status', 'waiting')->count(),
                'in_progress' => Queue::where('status', 'in_progress')->count(),
                'completed_today' => Queue::where('status', 'completed')
                    ->whereDate('created_at', today())->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
