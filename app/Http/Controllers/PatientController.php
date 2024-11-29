<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Patient::class);
        // Fetch and return patients
    }

    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);
        // Show patient details
    }

    public function update(Patient $patient)
    {
        $this->authorize('update', $patient);
        // Update patient
    }
}
