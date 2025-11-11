<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $animals = Animal::with('category')
            ->where('is_visible', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $animals,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:animal_categories,id',
            'name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'habitat' => 'nullable|string',
            'conservation_status' => 'nullable|string',
            'diet' => 'nullable|array',
            'age' => 'nullable|string',
            'weight' => 'nullable|string',
            'size' => 'nullable|string',
            'fun_facts' => 'nullable|string',
            'feeding_times' => 'nullable|array',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
        ]);

        $animal = Animal::create($validated);

        return response()->json([
            'success' => true,
            'data' => $animal,
            'message' => 'Animal created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $animal = Animal::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $animal,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $animal = Animal::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'exists:animal_categories,id',
            'name' => 'string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description' => 'string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'habitat' => 'nullable|string',
            'conservation_status' => 'nullable|string',
            'diet' => 'nullable|array',
            'age' => 'nullable|string',
            'weight' => 'nullable|string',
            'size' => 'nullable|string',
            'fun_facts' => 'nullable|string',
            'feeding_times' => 'nullable|array',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
        ]);

        $animal->update($validated);

        return response()->json([
            'success' => true,
            'data' => $animal,
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
