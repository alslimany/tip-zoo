<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(): JsonResponse
    {
        $activities = Activity::with(['facility', 'animal'])
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    public function today(): JsonResponse
    {
        $activities = Activity::with(['facility', 'animal'])
            ->where('is_active', true)
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'activity_type' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'facility_id' => 'nullable|exists:facilities,id',
            'animal_id' => 'nullable|exists:animals,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'recurrence' => 'nullable|array',
            'duration_minutes' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'requires_booking' => 'boolean',
            'price' => 'nullable|numeric',
            'age_restriction' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $activity = Activity::create($validated);

        return response()->json([
            'success' => true,
            'data' => $activity,
            'message' => 'Activity created successfully',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $activity = Activity::with(['facility', 'animal'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $activity = Activity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'activity_type' => 'string',
            'description' => 'string',
            'image' => 'nullable|string',
            'facility_id' => 'nullable|exists:facilities,id',
            'animal_id' => 'nullable|exists:animals,id',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
            'recurrence' => 'nullable|array',
            'duration_minutes' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'requires_booking' => 'boolean',
            'price' => 'nullable|numeric',
            'age_restriction' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $activity->update($validated);

        return response()->json([
            'success' => true,
            'data' => $activity,
            'message' => 'Activity updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted successfully',
        ]);
    }
}
