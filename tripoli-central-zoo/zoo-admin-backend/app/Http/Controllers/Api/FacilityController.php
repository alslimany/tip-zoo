<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FacilityController extends Controller
{
    public function index(): JsonResponse
    {
        $facilities = Facility::with('facilityType')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $facilities,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_type_id' => 'required|exists:facility_types,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'opening_hours' => 'nullable|array',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'amenities' => 'nullable|array',
            'is_accessible' => 'boolean',
            'is_open' => 'boolean',
            'capacity' => 'nullable|integer',
            'display_order' => 'integer',
        ]);

        $facility = Facility::create($validated);

        return response()->json([
            'success' => true,
            'data' => $facility,
            'message' => 'Facility created successfully',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $facility = Facility::with('facilityType')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $facility,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);

        $validated = $request->validate([
            'facility_type_id' => 'exists:facility_types,id',
            'name' => 'string|max:255',
            'description' => 'string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'opening_hours' => 'nullable|array',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'amenities' => 'nullable|array',
            'is_accessible' => 'boolean',
            'is_open' => 'boolean',
            'capacity' => 'nullable|integer',
            'display_order' => 'integer',
        ]);

        $facility->update($validated);

        return response()->json([
            'success' => true,
            'data' => $facility,
            'message' => 'Facility updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facility deleted successfully',
        ]);
    }
}
