<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnimalController extends Controller
{
    public function index()
    {
        $animals = Animal::with('category')->orderBy('display_order')->paginate(20);
        return view('admin.animals.index', compact('animals'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.animals.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'scientific_name' => 'nullable|string|max:255',
            'habitat' => 'nullable|string',
            'conservation_status' => 'nullable|string',
            'age' => 'nullable|string',
            'weight' => 'nullable|string',
            'size' => 'nullable|string',
            'facts' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'featured' => 'boolean',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('animals', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        Animal::create($validated);

        return redirect()->route('admin.animals.index')
            ->with('success', 'Animal created successfully.');
    }

    public function edit(Animal $animal)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.animals.edit', compact('animal', 'categories'));
    }

    public function update(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'scientific_name' => 'nullable|string|max:255',
            'habitat' => 'nullable|string',
            'conservation_status' => 'nullable|string',
            'age' => 'nullable|string',
            'weight' => 'nullable|string',
            'size' => 'nullable|string',
            'facts' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'featured' => 'boolean',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($animal->image_url) {
                $oldPath = str_replace('/storage/', '', $animal->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('animals', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $animal->update($validated);

        return redirect()->route('admin.animals.index')
            ->with('success', 'Animal updated successfully.');
    }

    public function destroy(Animal $animal)
    {
        // Delete image if exists
        if ($animal->image_url) {
            $path = str_replace('/storage/', '', $animal->image_url);
            Storage::disk('public')->delete($path);
        }

        $animal->delete();

        return redirect()->route('admin.animals.index')
            ->with('success', 'Animal deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        Animal::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' animals deleted successfully.'
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status');

        Animal::whereIn('id', $ids)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' animals updated successfully.'
        ]);
    }
}
