<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('display_order')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $types = ['animal' => 'Animal', 'facility' => 'Facility'];
        return view('admin.categories.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:animal,facility',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $types = ['animal' => 'Animal', 'facility' => 'Facility'];
        return view('admin.categories.edit', compact('category', 'types'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:animal,facility',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if category is in use
        if ($category->type === 'animal' && $category->animals()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has animals assigned to it.');
        }
        
        if ($category->type === 'facility' && $category->facilities()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has facilities assigned to it.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
