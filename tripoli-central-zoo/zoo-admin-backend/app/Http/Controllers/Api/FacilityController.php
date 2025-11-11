<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FacilityController extends Controller
{
    /**
     * Display a paginated listing of facilities with filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Facility::with(['category', 'openingHours', 'activities']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('accessible')) {
            $query->where('is_accessible', $request->boolean('accessible'));
        }

        // Apply scopes
        if ($request->boolean('open_only')) {
            $query->open();
        }

        if ($request->boolean('accessible_only')) {
            $query->accessible();
        }

        // Order by
        $query->orderBy('display_order')
            ->orderBy('name');

        // Paginate
        $perPage = $request->input('per_page', 15);
        $facilities = $query->paginate($perPage);

        return FacilityResource::collection($facilities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $facility = Facility::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new FacilityResource($facility->load(['category', 'openingHours'])),
            'message' => 'Facility created successfully',
        ], 201);
    }

    /**
     * Display full facility details with all relationships.
     */
    public function show(string $id): FacilityResource
    {
        $facility = Facility::with(['category', 'openingHours', 'activities'])
            ->findOrFail($id);

        return new FacilityResource($facility);
    }

    /**
     * Search facilities by query string.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->input('query', $request->input('q', ''));

        if (empty($query)) {
            return FacilityResource::collection([]);
        }

        $facilities = Facility::with(['category', 'openingHours'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('type', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->open()
            ->orderBy('name')
            ->limit(20)
            ->get();

        return FacilityResource::collection($facilities);
    }

    /**
     * Get facilities by type.
     */
    public function byType(string $type): AnonymousResourceCollection
    {
        $facilities = Facility::with(['category', 'openingHours', 'activities'])
            ->where('type', $type)
            ->open()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return FacilityResource::collection($facilities);
    }

    /**
     * Get facilities near specific coordinates.
     */
    public function nearby(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'radius' => 'nullable|numeric|min:0',
        ]);

        $x = $request->input('x');
        $y = $request->input('y');
        $radius = $request->input('radius', 100); // Default radius in meters

        $facilities = Facility::with(['category', 'openingHours'])
            ->whereNotNull('location_x')
            ->whereNotNull('location_y')
            ->open()
            ->get()
            ->filter(function ($facility) use ($x, $y, $radius) {
                // Calculate distance using Euclidean distance
                // For more accurate geographic distance, use Haversine formula
                $distance = sqrt(
                    pow($facility->location_x - $x, 2) + 
                    pow($facility->location_y - $y, 2)
                );
                return $distance <= $radius;
            })
            ->sortBy(function ($facility) use ($x, $y) {
                // Sort by distance
                return sqrt(
                    pow($facility->location_x - $x, 2) + 
                    pow($facility->location_y - $y, 2)
                );
            })
            ->values();

        return FacilityResource::collection($facilities);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);
        $facility->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new FacilityResource($facility->load(['category', 'openingHours'])),
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
