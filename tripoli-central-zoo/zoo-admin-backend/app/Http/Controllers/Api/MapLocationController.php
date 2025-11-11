<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MapLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MapLocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MapLocation::query();

        if ($request->has('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        if ($request->has('map_level')) {
            $query->where('map_level', $request->map_level);
        }

        $locations = $query->where('is_interactive', true)->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_type' => 'required|in:animal,facility,activity',
            'reference_id' => 'required|integer',
            'coordinate_x' => 'required|numeric',
            'coordinate_y' => 'required|numeric',
            'svg_path' => 'nullable|array',
            'map_level' => 'integer',
            'description' => 'nullable|string',
            'is_interactive' => 'boolean',
        ]);

        $location = MapLocation::create($validated);

        return response()->json([
            'success' => true,
            'data' => $location,
            'message' => 'Map location created successfully',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $location = MapLocation::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $location,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $location = MapLocation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'location_type' => 'in:animal,facility,activity',
            'reference_id' => 'integer',
            'coordinate_x' => 'numeric',
            'coordinate_y' => 'numeric',
            'svg_path' => 'nullable|array',
            'map_level' => 'integer',
            'description' => 'nullable|string',
            'is_interactive' => 'boolean',
        ]);

        $location->update($validated);

        return response()->json([
            'success' => true,
            'data' => $location,
            'message' => 'Map location updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $location = MapLocation::findOrFail($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Map location deleted successfully',
        ]);
    }
}
