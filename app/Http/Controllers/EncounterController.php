<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Services\EncounterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EncounterController extends Controller
{
    protected $encounterService;

    public function __construct(EncounterService $encounterService)
    {
        $this->encounterService = $encounterService;
    }

    /**
     * Start encounter
     */
    public function start(Encounter $encounter): JsonResponse
    {
        try {
            $updatedEncounter = $this->encounterService->startEncounter($encounter);

            return response()->json([
                'success' => true,
                'message' => 'Encounter started successfully',
                'data' => $updatedEncounter->load(['patient', 'doctor'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start encounter',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete encounter
     */
    public function complete(Request $request, Encounter $encounter): JsonResponse
    {
        $request->validate([
            'diagnosis' => 'nullable|string|max:255',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        try {
            $completionData = $request->only(['diagnosis', 'treatment_plan', 'follow_up_date']);
            $updatedEncounter = $this->encounterService->completeEncounter($encounter, $completionData);

            return response()->json([
                'success' => true,
                'message' => 'Encounter completed successfully',
                'data' => $updatedEncounter->load(['patient', 'doctor', 'medications', 'prescriptions'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete encounter',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
