<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnimalController extends Controller
{
    /**
     * Display a paginated listing of animals with filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Animal::with(['category', 'openingHours', 'activities']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Apply scopes
        if ($request->boolean('active_only')) {
            $query->active();
        }

        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Order by
        $query->orderBy('display_order')
            ->orderBy('name');

        // Paginate
        $perPage = $request->input('per_page', 15);
        $animals = $query->paginate($perPage);

        return AnimalResource::collection($animals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $animal = Animal::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AnimalResource($animal->load(['category', 'openingHours'])),
            'message' => 'Animal created successfully',
        ], 201);
    }

    /**
     * Display full animal details with all relationships.
     */
    public function show(string $id): AnimalResource
    {
        $animal = Animal::with(['category', 'openingHours', 'activities'])
            ->findOrFail($id);

        return new AnimalResource($animal);
    }

    /**
     * Search animals by query string.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->input('query', $request->input('q', ''));

        if (empty($query)) {
            return AnimalResource::collection([]);
        }

        $animals = Animal::with(['category', 'openingHours'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('species', 'like', "%{$query}%")
                  ->orWhere('scientific_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('facts', 'like', "%{$query}%");
            })
            ->active()
            ->orderBy('featured', 'desc')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return AnimalResource::collection($animals);
    }

    /**
     * Get animals by category.
     */
    public function byCategory(string $categoryId): AnonymousResourceCollection
    {
        $animals = Animal::with(['category', 'openingHours', 'activities'])
            ->where('category_id', $categoryId)
            ->active()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return AnimalResource::collection($animals);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $animal = Animal::findOrFail($id);
        $animal->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new AnimalResource($animal->load(['category', 'openingHours'])),
            'message' => 'Animal updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $animal = Animal::findOrFail($id);
        $animal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Animal deleted successfully',
        ]);
    }
}
